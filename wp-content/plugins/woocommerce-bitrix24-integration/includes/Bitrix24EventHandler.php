<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Actions\OrderNoteAction;

class Bitrix24EventHandler
{
    private static $instance = false;

    protected function __construct()
    {
        if (!$this->isEnabled()) {
            return;
        }

        if (
            !isset($_GET['wcbx24_update_lead_handler'])
            && !isset($_GET['wcbx24_update_deal_handler'])
            && !isset($_GET[Bootstrap::BITRIX24_HANDLER_GET_KEY])
        ) {
            return;
        }

        add_action('init', [$this, 'handler'], PHP_INT_MAX);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function handler()
    {
        if (empty($_POST) || empty($_POST['auth']) || empty($_POST['auth']['application_token'])) {
            exit;
        }

        Helper::log('[webhook] outbound webhook request', [$_POST['event'], $_POST['data']['FIELDS']['ID']]);

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        if ((string) $_POST['auth']['application_token'] !== (string) $settings['outbound-webhook']) {
            Helper::log('[webhook] token not coincided, value was received from Bitrix24', $_POST['auth']);

            exit;
        }

        $entryID = $_POST['data']['FIELDS']['ID'];

        if (empty($entryID)) {
            Helper::log('[webhook] empty entry id');

            exit;
        }

        // necessary to prevent cyclical behavior
        remove_action('woocommerce_order_status_changed', [OrderToBitrix24::getInstance(), 'afterSaveOrder']);
        remove_action('woocommerce_after_order_object_save', [OrderToBitrix24::getInstance(), 'afterSaveOrder']);

        $statuses = [];
        $entryType = '';
        $metaKey = '';

        switch ($_POST['event']) {
            case 'ONCRMLEADUPDATE':
                if ($settings['type'] !== 'lead') {
                    Helper::log('[webhook] ignore processing as setting `type` - ' . $settings['type']);

                    exit;
                }

                $metaKey = '_wc_bitrix24_lead_id';
                $entryType = 'lead';
                $statuses = $settings['lead_statuses'] ?? [];
                break;
            case 'ONCRMDEALUPDATE':
                if ($settings['type'] === 'lead') {
                    Helper::log('[webhook] ignore processing as setting `type` - ' . $settings['type']);

                    exit;
                }

                $metaKey = '_wc_bitrix24_deal_id';
                $entryType = 'deal';
                $statuses = $settings['deal_statuses'] ?? [];
                break;
            case 'ONCRMLEADDELETE':
                if ($settings['type'] !== 'lead') {
                    Helper::log('[webhook] ignore processing as setting `type` - ' . $settings['type']);

                    exit;
                }

                $this->processingDeleteEvent('_wc_bitrix24_lead_id', $entryID);

                exit;
            case 'ONCRMDEALDELETE':
                if ($settings['type'] === 'lead') {
                    Helper::log('[webhook] ignore processing as setting `type` - ' . $settings['type']);

                    exit;
                }

                $this->processingDeleteEvent('_wc_bitrix24_deal_id', $entryID);

                exit;
            case 'ONCRMTIMELINECOMMENTADD':
                $this->processingNote($entryID);

                exit;
            default:
                // Nothing
                break;
        }

        if (empty($metaKey) || empty($statuses)) {
            Helper::log('[webhook] empty status or unknown event');

            exit;
        }

        // delay is required since adding a note also raises an update event
        // this can create an incorrect behavior with a reverse change in order status
        sleep(5);

        $siteOrderId = $this->getSiteOrderId($metaKey, $entryID);

        if (!$siteOrderId) {
            Helper::log('[webhook] no order by data', [
                'key' => $metaKey,
                'value' => $entryID,
            ]);

            exit;
        }

        $entry = Crm::sendApiRequest('crm.' . $entryType . '.get', false, ['id' => $entryID], true);

        if (empty($entry)) {
            Helper::log('[webhook] no entry in Bitrix24 by data', [
                'key' => $metaKey,
                'value' => $entryID,
            ]);

            exit;
        }

        $order = \wc_get_order($siteOrderId);

        if (!$order) {
            exit;
        }

        Helper::log('[webhook] order exists by entry id - ' . $entryID . ', order id - ' . $order->get_id());

        // check exists pending send event on this order
        if (\as_next_scheduled_action(Bootstrap::CRON_TASK_SEND, [$order->get_id()])) {
            Helper::log('[webhook] ignore, there is a pending order send event, otherwise a status collision is possible');

            exit;
        }

        if (apply_filters('wc_bitrix24_plugin_apply_product_list_changes', false)) {
            $productRows = Crm::sendApiRequest('crm.' . $entryType . '.productrows.get', false, ['id' => $entryID], true);
            $currentBitrix24Data = [];

            foreach ($productRows as $productRow) {
                $bitrixProduct = Crm::sendApiRequest(
                    'crm.product.list',
                    false,
                    [
                        'FILTER' => ['ID' => $productRow['PRODUCT_ID']],
                        'SELECT' => ['ID', 'XML_ID'],
                    ]
                );

                if ($bitrixProduct && $bitrixProduct[0]['XML_ID']) {
                    $currentBitrix24Data[$bitrixProduct[0]['XML_ID']] = [
                        'price' => $productRow['PRICE'],
                        'qty' => $productRow['QUANTITY'],
                        'total' => $productRow['PRICE'] * $productRow['QUANTITY'],
                    ];
                }
            }

            do_action('wc_bitrix24_plugin_crm_current_product_list', $order, $currentBitrix24Data);
        }

        if ($entryType === 'lead') {
            $this->resolveLead($order, $entry);
        } else {
            $this->resolveDeal($order, $entry);
        }

        exit;
    }

    private function resolveLead($order, $entry)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);
        $statuses = $settings['lead_statuses'] ?? [];

        if (empty($statuses)) {
            Helper::log('[webhook] status mapping not configured');

            exit;
        }

        Helper::log('[webhook] current lead status - ' . $entry['STATUS_ID'] . ', order status - ' . $order->get_status());

        if (empty($statuses[$order->get_status()])) {
            Helper::log(
                '[webhook] the `status` of the `lead` is not specified for the current order status, so we cannot change this status',
                $statuses
            );

            exit;
        }

        Helper::log('[webhook] current status mapping', $statuses);

        foreach ($statuses as $orderStatus => $leadStatus) {
            if (
                (string) $leadStatus !== (string) $entry['STATUS_ID']
                || $orderStatus === $order->get_status()
            ) {
                continue;
            }

            Helper::log('[webhook] set new order status - ' . $orderStatus);

            $order->update_status(
                $orderStatus,
                esc_html__('Lead status changed. Lead ID #', 'wc-bitrix24-integration')
                . $entry['ID']
                . '. '
            );

            exit;
        }

        Helper::log('[webhook] the status of the lead is not specified in any of the corresponding statuses');

        exit;
    }

    private function resolveDeal($order, $entry)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);
        $statuses = $settings['deal_statuses'] ?? [];

        if (empty($statuses)) {
            Helper::log('[webhook] status mapping not configured');

            exit;
        }

        Helper::log(
            '[webhook] current deal stage - '
            . $entry['STAGE_ID']
            . ', category - '
            . $entry['CATEGORY_ID']
            . ', order status - '
            . $order->get_status()
        );

        if (empty($statuses[$order->get_status()])) {
            Helper::log(
                '[webhook] the `stage` of the `deal` is not specified for the current order status, so we cannot change this status',
                $statuses
            );

            exit;
        }

        Helper::log('[webhook] current status mapping', $statuses);

        foreach ($statuses as $orderStatus => $dealStatus) {
            $checkStatuses = [];
            $checkStatuses[] = (string) $entry['STAGE_ID'];

            if (!empty($entry['CATEGORY_ID'])) {
                $checkStatuses[] = $entry['CATEGORY_ID'] . '||||' . (string) $entry['STAGE_ID'];
            }

            if (
                !in_array((string) $dealStatus, $checkStatuses)
                || $orderStatus === $order->get_status()
            ) {
                continue;
            }

            Helper::log('[webhook] set new order status - ' . $orderStatus);

            $order->update_status(
                $orderStatus,
                esc_html__('Deal status changed. Deal ID #', 'wc-bitrix24-integration')
                . $entry['ID']
                . '. '
            );

            exit;
        }

        Helper::log('[webhook] the stage of the deal is not specified in any of the corresponding statuses (or the expected status is already set)');

        exit;
    }

    /**
     * @param string $metaKey
     * @param int    $crmId
     *
     * @return void
     */
    private function processingDeleteEvent($metaKey, $crmId)
    {
        $settings = \get_option(Bootstrap::OPTIONS_KEY, []);
        $siteOrderId = $this->getSiteOrderId($metaKey, $crmId);

        if (!$siteOrderId) {
            Helper::log('[webhook] no order by data', [
                'key' => $metaKey,
                'value' => $crmId,
            ]);

            return;
        }

        $order = \wc_get_order($siteOrderId);

        if (!$order) {
            Helper::log('[webhook] empty result `wc_get_order`, ID - ' . $siteOrderId);

            return;
        }

        Helper::log('[webhook] resolved site order, ID - ' . $siteOrderId);

        $deleteAction = $settings['outbound-webhook-remove-action'] ?? '';

        if (empty($deleteAction)) {
            Helper::log('[webhook] action not chosen');

            return;
        }

        if ($deleteAction === 'completely') {
            $order->delete(true);

            Helper::log('[webhook] order completely removed, ID - ' . $siteOrderId);

            return;
        }

        remove_action('wp_insert_comment', [OrderNoteAction::getInstance(), 'action'], PHP_INT_MAX);

        $order->delete(false);

        Helper::log('[webhook] order moved to trash, ID - ' . $siteOrderId);

        $order->add_order_note(esc_html__('The order was moved to the trash when deleting a deal / lead in the CRM.', 'wc-bitrix24-integration'), true);
    }

    private function processingNote($entryID)
    {
        $entry = Crm::sendApiRequest('crm.timeline.comment.get', false, ['id' => $entryID], true);

        if (!$entry) {
            return;
        }

        $note = html_entity_decode($entry['COMMENT']);

        if (
            empty($note)
            || strpos($note, '[PUBLIC]') === false
        ) {
            return;
        }

        $siteOrderId = $this->getSiteOrderId(
            $entry['ENTITY_TYPE'] === 'lead' ? '_wc_bitrix24_lead_id' : '_wc_bitrix24_deal_id',
            $entry['ENTITY_ID']
        );

        if (!$siteOrderId) {
            exit;
        }

        $order = \wc_get_order($siteOrderId);

        if (!$order) {
            exit;
        }

        remove_action('wp_insert_comment', [OrderNoteAction::getInstance(), 'action'], PHP_INT_MAX);

        $note = str_replace('[PUBLIC]', '', $note);
        $order->add_order_note($note, true);
    }

    /**
     * @param string $metaKey
     * @param int    $crmId
     *
     * @return false|int
     */
    private function getSiteOrderId($metaKey, $crmId)
    {
        $orders = \wc_get_orders(
            [
                'type' => 'shop_order',
                'status' => 'any',
                'limit' => 1,
                'meta_key' => $metaKey,
                'meta_value' => $crmId,
                'meta_compare' => '=',
                'return' => 'ids',
            ]
        );

        return !empty($orders) ? $orders[0] : false;
    }

    private function isEnabled()
    {
        if (!Helper::isVerify()) {
            return false;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        return Helper::isEnabled()
            && !empty($settings['outbound-webhook']);
    }
}
