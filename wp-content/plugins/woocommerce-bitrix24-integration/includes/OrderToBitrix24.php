<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes;

use Atum\Inc\Globals;
use Itgalaxy\PluginCommon\ActionSchedulerHelper;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\AdditionalOptionsDataPreparer;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\OrderMetaDataPreparer;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\OrderUserStatDataPreparer;

class OrderToBitrix24
{
    /**
     * @var \WC_Order
     */
    public $order;

    public $bitrixEntry = [];

    private static $instance = false;

    protected function __construct()
    {
        if (!Helper::isEnabled() || !Helper::isVerify()) {
            return;
        }

        add_action('woocommerce_checkout_order_processed', [$this, 'actionProcessing'], 11, 1);

        // support checkout block
        add_action('woocommerce_store_api_checkout_order_processed', [$this, 'actionProcessing'], 11, 1);

        add_action('woocommerce_resume_order', [$this, 'actionProcessing'], 10, 1);
        add_action('woocommerce_order_status_changed', [$this, 'afterSaveOrder']);

        // compatible with Woocommerce Subscriptions - renewal order
        add_filter('wcs_renewal_order_created', [$this, 'renewalOrder'], 10, 2);

        add_action('cartflows_offer_accepted', [$this, 'cartFlowsProCompatible'], 10, 1);
        add_action('cartflows_offer_rejected', [$this, 'cartFlowsProCompatible'], 10, 1);

        add_action('woocommerce_after_order_object_save', [$this, 'afterSaveOrder']);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function cartFlowsProCompatible($order)
    {
        // if is order number
        if (is_numeric($order)) {
            $this->actionProcessing($order);
        }
        // if is order object
        elseif (is_a($order, 'WC_Order')) {
            $this->actionProcessing($order->get_id());
        }
    }

    public function renewalOrder($renewalOrder, $subscription)
    {
        // if is order number
        if (is_numeric($renewalOrder)) {
            $this->actionProcessing($renewalOrder);
        }
        // if is order object
        elseif (is_a($renewalOrder, 'WC_Order')) {
            $this->actionProcessing($renewalOrder->get_id());
        }

        return $renewalOrder;
    }

    public function afterSaveOrder($order)
    {
        $orderID = false;

        // if is order number
        if (is_numeric($order)) {
            $orderID = $order;
        }
        // if is order object
        elseif (is_a($order, 'WC_Order')) {
            $orderID = $order->get_id();
        }

        $this->registerSendEvent($orderID);
    }

    public function actionProcessing($order)
    {
        $orderID = false;

        // if is order number
        if (is_numeric($order)) {
            $orderID = $order;
        }
        // if is order object
        elseif (is_a($order, 'WC_Order')) {
            $orderID = $order->get_id();
        }

        Helper::log('action processing - ' . $orderID);

        // prevent duplicate
        \remove_action('woocommerce_after_order_object_save', [$this, 'afterSaveOrder']);

        OrderUserStatDataPreparer::save($orderID);

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        if (!empty($settings['send_type']) && $settings['send_type'] === 'wp_cron') {
            $this->registerSendEvent($orderID);
        } else {
            $this->orderSendCrm($orderID);
        }
    }

    public function registerSendEvent($orderID)
    {
        if (!$orderID) {
            return;
        }
        /**
         * int - pending exists
         * false - not exists
         * true - running exists.
         */
        $alreadyScheduled = \as_next_scheduled_action(Bootstrap::CRON_TASK_SEND, [$orderID]);

        Helper::log('check scheduled value - ' . $orderID, [$alreadyScheduled]);

        if (!is_bool($alreadyScheduled)) {
            return;
        }

        $offset = (int) apply_filters('wc_bitrix24_plugin_wp_cron_order_event_timeout_wait_before_submit', 15) +
            (5 * ActionSchedulerHelper::getCountPendingActions(Bootstrap::CRON_TASK_SEND));

        \as_schedule_single_action(time() + $offset, Bootstrap::CRON_TASK_SEND, [$orderID]);

        Helper::log(
            'register task send - ' . $orderID
            . ', start - ' . date('Y-m-d H:i:s', time() + $offset)
            . ', count events - ' . ActionSchedulerHelper::getCountPendingActions(Bootstrap::CRON_TASK_SEND)
        );
    }

    public function orderSendCrm($orderID)
    {
        // prevent duplicate
        \remove_action('woocommerce_after_order_object_save', [$this, 'afterSaveOrder']);

        $this->order = \wc_get_order($orderID);

        if (apply_filters('wc_bitrix24_plugin_do_not_send_order', false, $this->order)) {
            Helper::log('do not send order - ' . $this->order->get_id());

            return;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        if ($settings['type'] === 'lead') {
            if (
                $this->order->get_meta('_wc_bitrix24_lead_id', true)
                && $this->checkExists($settings['type'])
            ) {
                Helper::log('update lead event - ' . $this->order->get_id());
                $this->leadUpdate();
            } else {
                Helper::log('create lead event - ' . $this->order->get_id());
                $this->leadCreate();
            }

            return;
        }

        if ($settings['type'] === 'contact_only') {
            Helper::log('`contact_only` event - ' . $this->order->get_id());

            $this->onlyContact();

            return;
        }

        if (
            $this->order->get_meta('_wc_bitrix24_deal_id', true)
            && $this->checkExists($settings['type'])
        ) {
            Helper::log('update deal event - ' . $this->order->get_id());
            $this->dealUpdate();
        } else {
            Helper::log('start create deal event - ' . $this->order->get_id());
            $this->dealCreate();
            Helper::log('end create deal event - ' . $this->order->get_id());
        }
    }

    private function onlyContact()
    {
        if ($this->order->get_meta('_wc_bx24_order_sent', true)) {
            Helper::log('has already been sent, ignoring - ' . $this->order->get_id());

            return;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        $orderData = $this->prepareOrderData();

        $crmFields['contact'] = CrmFields::mixedContactAddressFields(get_option(Bootstrap::CONTACT_FIELDS_KEY, []));
        $sendFields['customer_id'] = $this->order->get_customer_id();

        $sendFields['contact'] = $this->prepareFields($settings['contact'], $orderData);
        $sendFields['contact']['ADDRESS_COUNTRY'] = \WC()->countries->countries[$this->order->get_billing_country()];

        if ($this->order->get_shipping_country()) {
            $sendFields['contact']['ADDRESS_COUNTRY-11'] = \WC()->countries->countries[$this->order->get_shipping_country()];
        }

        $sendFields['contact']['COMMENTS'] = $this->order->get_customer_note();

        if (
            isset($settings['contact_update_exists'])
            && (int) $settings['contact_update_exists'] === 1
        ) {
            $sendFields['contact_update_exists'] = true;
        }

        Crm::send($sendFields, $crmFields, 'contact');

        $this->order->update_meta_data('_wc_bx24_order_sent', 1);
        $this->order->save_meta_data();
    }

    private function checkExists($type)
    {
        if ($type === 'lead') {
            $result = Crm::sendApiRequest(
                'crm.lead.get',
                false,
                [
                    'id' => $this->order->get_meta('_wc_bitrix24_lead_id', true),
                ],
                true
            );

            if (empty($result)) {
                Helper::log('check exists - no entry in Bitrix24 by data', [
                    'key' => '_wc_bitrix24_lead_id',
                    'value' => $this->order->get_meta('_wc_bitrix24_lead_id', true),
                ]);

                return false;
            }

            $this->bitrixEntry = $result;

            return true;
        }

        $result = Crm::sendApiRequest(
            'crm.deal.get',
            false,
            [
                'id' => $this->order->get_meta('_wc_bitrix24_deal_id', true),
            ],
            true
        );

        if (empty($result)) {
            Helper::log('check exists - no entry in Bitrix24 by data', [
                'key' => '_wc_bitrix24_deal_id',
                'value' => $this->order->get_meta('_wc_bitrix24_deal_id', true),
            ]);

            return false;
        }

        $this->bitrixEntry = $result;

        return true;
    }

    private function leadUpdate()
    {
        $leadID = $this->order->get_meta('_wc_bitrix24_lead_id', true);

        if (!$leadID) {
            Helper::log('empty lead by order ID - ' . $this->order->get_id());

            return;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);
        $status = $this->order->get_status();

        // CARTFLOWS_PRO_FILE - define CartFlows Pro plugin
        if (!empty($settings['resend_product_list']) || defined('CARTFLOWS_PRO_FILE')) {
            $this->setProductRows($leadID, 'lead');
        }

        $orderData = $this->prepareOrderData();
        $leadFields = $this->prepareFields($settings['lead'], $orderData);
        $leadFields = Crm::prepareFields(get_option(Bootstrap::LEAD_FIELDS_KEY, []), $leadFields);

        $updateLeadFields = [];

        if (empty($settings['do_not_post_status_changes'])) {
            $leadStatuses = $settings['lead_statuses'] ?? [];

            if (!empty($leadStatuses) && !empty($leadStatuses[$status])) {
                $updateLeadFields['STATUS_ID'] = $leadStatuses[$status];
            } else {
                Helper::log('empty status mapping or not set for current status - ' . $status, $leadStatuses);
            }

            if (
                !empty($updateLeadFields['STATUS_ID'])
                && $this->bitrixEntry['STATUS_ID'] === $updateLeadFields['STATUS_ID']
            ) {
                Helper::log('expected status is already set - ' . $this->bitrixEntry['STATUS_ID']);
                unset($updateLeadFields['STATUS_ID']);
            }
        } else {
            Helper::log('enabled `do_not_post_status_changes`');
        }

        if (!empty($settings['lead']['update'])) {
            foreach (array_keys($settings['lead']['update']) as $fieldID) {
                if (!isset($leadFields[$fieldID]) || $leadFields[$fieldID] === '') {
                    continue;
                }

                $updateLeadFields[$fieldID] = $leadFields[$fieldID];
            }

            // fix duplicate phone
            if (
                !empty($this->bitrixEntry['PHONE'])
                && !empty($updateLeadFields['PHONE'])
                && Crm::existEmailPhone($this->bitrixEntry['PHONE'], $updateLeadFields['PHONE'][0]['VALUE'])
            ) {
                unset($updateLeadFields['PHONE']);
            }

            // fix duplicate email
            if (
                !empty($this->bitrixEntry['EMAIL'])
                && !empty($updateLeadFields['EMAIL'])
                && Crm::existEmailPhone($this->bitrixEntry['EMAIL'], $updateLeadFields['EMAIL'][0]['VALUE'])
            ) {
                unset($updateLeadFields['EMAIL']);
            }
        }

        $updateLeadFields = apply_filters('wc_bitrix24_plugin_lead_fields_before_send', $updateLeadFields, $this->order);

        if (empty($updateLeadFields)) {
            return;
        }

        Crm::sendApiRequest(
            'crm.lead.update',
            false,
            [
                'id' => $leadID,
                'fields' => $updateLeadFields,
            ]
        );
    }

    private function dealUpdate()
    {
        $dealID = $this->order->get_meta('_wc_bitrix24_deal_id', true);

        if (!$dealID) {
            Helper::log('empty deal by order ID - ' . $this->order->get_id());

            return;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);
        $status = $this->order->get_status();

        // CARTFLOWS_PRO_FILE - define CartFlows Pro plugin
        if (!empty($settings['resend_product_list']) || defined('CARTFLOWS_PRO_FILE')) {
            $this->setProductRows($dealID, 'deal');
        }

        $orderData = $this->prepareOrderData();
        $dealFields = $this->prepareFields($settings['deal'], $orderData);
        $dealFields = Crm::prepareFields(get_option(Bootstrap::DEAL_FIELDS_KEY, []), $dealFields);

        $updateDealFields = [];

        if (empty($settings['do_not_post_status_changes'])) {
            $dealStatuses = $settings['deal_statuses'] ?? [];

            if (!empty($dealStatuses) && !empty($dealStatuses[$status])) {
                // Pipeline support
                $isPipelineStatus = explode(':', $dealStatuses[$status]);
                $pipelineID = 0;

                if (count($isPipelineStatus) === 2) {
                    $pipelineID = (int) str_replace('C', '', $isPipelineStatus[0]);
                } else {
                    $isPipelineStatus = explode('||||', $dealStatuses[$status]);

                    if (count($isPipelineStatus) == 2) {
                        $pipelineID = $isPipelineStatus[0];
                        $dealStatuses[$status] = $isPipelineStatus[1];
                    }
                }

                // different pipeline
                if ((int) $this->bitrixEntry['CATEGORY_ID'] === $pipelineID) {
                    $updateDealFields['STAGE_ID'] = $dealStatuses[$status];
                } else {
                    Helper::log(
                        '(deal update) different pipeline, site - '
                        . $pipelineID
                        . ', deal entry - '
                        . (int) $this->bitrixEntry['CATEGORY_ID']
                    );
                }
            } else {
                Helper::log('empty status mapping or not set for current status - ' . $status, $dealStatuses);
            }

            if (
                !empty($updateDealFields['STAGE_ID'])
                && $this->bitrixEntry['STAGE_ID'] === $updateDealFields['STAGE_ID']
            ) {
                Helper::log('expected stage is already set - ' . $this->bitrixEntry['STAGE_ID']);
                unset($updateDealFields['STAGE_ID']);
            }
        } else {
            Helper::log('enabled `do_not_post_status_changes`');
        }

        if (!empty($settings['deal']['update'])) {
            foreach (array_keys($settings['deal']['update']) as $fieldID) {
                if (!isset($dealFields[$fieldID]) || $dealFields[$fieldID] === '') {
                    continue;
                }

                $updateDealFields[$fieldID] = $dealFields[$fieldID];
            }
        }

        // If for some reason the deal was left without contact, we will try to fix it
        if (
            !$this->order->get_meta('_wc_bx24_order_has_try_fix_contact', true)
            && empty($this->bitrixEntry['CONTACT_ID'])
        ) {
            $crmFields = [];
            $crmFields['contact'] = CrmFields::mixedContactAddressFields(get_option(Bootstrap::CONTACT_FIELDS_KEY, []));
            $crmFields['company'] = get_option(Bootstrap::COMPANY_FIELDS_KEY, []);
            $crmFields['deal'] = get_option(Bootstrap::DEAL_FIELDS_KEY, []);

            $sendFields = [
                'is_deal_update_request' => true,
            ];
            $sendFields['deal'] = $dealFields;
            $sendFields = $this->prepareContactAndCompanyData($sendFields, $orderData);

            $contactData = Crm::send($sendFields, $crmFields, 'deal');

            if (!empty($contactData['CONTACT_ID'])) {
                $updateDealFields['CONTACT_ID'] = $contactData['CONTACT_ID'];
            }

            if (!empty($contactData['COMPANY_ID'])) {
                $updateDealFields['COMPANY_ID'] = $contactData['COMPANY_ID'];
            }

            $this->order->update_meta_data('_wc_bx24_order_has_try_fix_contact', 1);
            $this->order->save_meta_data();
        }

        $updateDealFields = apply_filters('wc_bitrix24_plugin_deal_fields_before_send', $updateDealFields, $this->order);

        if (empty($updateDealFields)) {
            return;
        }

        Crm::sendApiRequest(
            'crm.deal.update',
            false,
            [
                'id' => $dealID,
                'fields' => $updateDealFields,
            ]
        );
    }

    private function leadCreate()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);
        $leadStatuses = $settings['lead_statuses'] ?? [];

        if (empty($leadStatuses)) {
            Helper::log('empty status mapping - ' . $this->order->get_id());

            return;
        }

        $status = $this->order->get_status();

        if (empty($leadStatuses[$status])) {
            Helper::log('empty status - ' . $status);

            return;
        }

        $leadFields = $settings['lead'];

        // drop enabled update field settings
        if (isset($leadFields['update'])) {
            unset($leadFields['update']);
        }

        $crmFields = get_option(Bootstrap::LEAD_FIELDS_KEY, []);

        $orderData = $this->prepareOrderData();
        $data = $this->order->get_data();
        $leadFields = $this->prepareFields($leadFields, $orderData);

        $leadFields['STATUS_ID'] = $leadStatuses[$status];
        $leadFields['CURRENCY_ID'] = $data['currency'];
        $leadFields['ADDRESS_COUNTRY'] = \WC()->countries->countries[$this->order->get_billing_country()];
        $leadFields['COMMENTS'] = $this->generateNote();

        if (empty($settings['lead']['TITLE'])) {
            $leadFields['TITLE'] = esc_html__('Order', 'wc-bitrix24-integration')
                . ' '
                . $this->order->get_order_number();
        } else {
            $leadFields['TITLE'] = $this->generateTitle($orderData, $settings['lead']['TITLE']);
        }

        // add admin order link if enable
        if (
            isset($settings['add_admin_order_link_lead_comment'])
            && $settings['add_admin_order_link_lead_comment'] === 'true'
        ) {
            if (!empty($leadFields['COMMENTS'])) {
                $leadFields['COMMENTS'] = Helper::getOrderLink($this->order)
                    . '<br>'
                    . $leadFields['COMMENTS'];
            } else {
                $leadFields['COMMENTS'] = Helper::getOrderLink($this->order);
            }
        }

        if (
            isset($settings['lead_not_send_product_list_only_summ'])
            && $settings['lead_not_send_product_list_only_summ'] === 'true'
        ) {
            $leadFields['OPPORTUNITY'] = $data['total'];
        }

        if (!empty($settings['lead_link_with_exists_contact'])) {
            $leadFields['link_with_exists_contact'] = true;
        }

        $leadFields = apply_filters('wc_bitrix24_plugin_lead_fields_before_send', $leadFields, $this->order);

        $lead = Crm::send(['lead' => $leadFields], ['lead' => $crmFields]);

        if ($lead) {
            $leadID = current($lead);

            Helper::log('created lead - ' . $leadID);

            $this->order->update_meta_data('_wc_bitrix24_lead_id', $leadID);

            // needs to be written anyway to clear the value if data was sent earlier
            $this->order->update_meta_data('_itglx_wc24_product_hash', '');

            $this->order->save_meta_data();

            $this->order->add_order_note(
                esc_html__('Added lead in CRM #', 'wc-bitrix24-integration')
                . '<a target="_blank" href="'
                . esc_url(Helper::getLeadLink($leadID))
                . '">'
                . $leadID
                . '</a>'
            );

            if (
                !isset($settings['lead_not_send_product_list_only_summ'])
                || $settings['lead_not_send_product_list_only_summ'] !== 'true'
            ) {
                $this->setProductRows($leadID, 'lead');
            }
        }
    }

    private function dealCreate()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);
        $dealStatuses = $settings['deal_statuses'] ?? [];

        if (empty($dealStatuses)) {
            Helper::log('empty status mapping - ' . $this->order->get_id());

            return;
        }

        $status = $this->order->get_status();

        if (empty($dealStatuses[$status])) {
            Helper::log('empty status - ' . $status);

            return;
        }

        $orderData = $this->prepareOrderData();
        $data = $this->order->get_data();

        $crmFields['deal'] = get_option(Bootstrap::DEAL_FIELDS_KEY, []);
        $crmFields['contact'] = CrmFields::mixedContactAddressFields(get_option(Bootstrap::CONTACT_FIELDS_KEY, []));
        $crmFields['company'] = get_option(Bootstrap::COMPANY_FIELDS_KEY, []);

        $sendFields['deal'] = $this->prepareFields($settings['deal'], $orderData);

        // drop enabled update field settings
        if (isset($sendFields['deal']['update'])) {
            unset($sendFields['deal']['update']);
        }

        $sendFields['deal']['STAGE_ID'] = $dealStatuses[$status];

        if (empty($settings['deal']['TITLE'])) {
            $sendFields['deal']['TITLE'] = esc_html__('Order', 'wc-bitrix24-integration')
                . ' '
                . $this->order->get_order_number();
        } else {
            $sendFields['deal']['TITLE'] = $this->generateTitle($orderData, $settings['deal']['TITLE']);
        }

        $sendFields['deal']['BEGINDATE'] = $this->order->get_date_created()->date_i18n('c');

        if ($this->order->get_date_completed()) {
            $sendFields['deal']['CLOSEDATE'] = $this->order->get_date_completed()->date_i18n('c');
        }

        $sendFields['deal']['CURRENCY_ID'] = $data['currency'];
        $sendFields['deal']['COMMENTS'] = $this->generateNote();

        // add admin order link if enable
        if (
            isset($settings['add_admin_order_link_deal_comment'])
            && $settings['add_admin_order_link_deal_comment'] === 'true'
        ) {
            if (!empty($sendFields['deal']['COMMENTS'])) {
                $sendFields['deal']['COMMENTS'] = Helper::getOrderLink($this->order)
                    . '<br>'
                    . $sendFields['deal']['COMMENTS'];
            } else {
                $sendFields['deal']['COMMENTS'] = Helper::getOrderLink($this->order);
            }
        }

        if (
            isset($settings['not_send_product_list_only_summ'])
            && $settings['not_send_product_list_only_summ'] === 'true'
        ) {
            $sendFields['deal']['OPPORTUNITY'] = $data['total'];
        }

        $sendFields = $this->prepareContactAndCompanyData($sendFields, $orderData);

        $sendFields['deal'] = apply_filters('wc_bitrix24_plugin_deal_fields_before_send', $sendFields['deal'], $this->order);

        $deal = Crm::send($sendFields, $crmFields, 'deal', $this->order);

        if ($deal) {
            $dealID = current($deal);

            Helper::log('created deal - ' . $dealID);

            $this->order->update_meta_data('_wc_bitrix24_deal_id', $dealID);

            // needs to be written anyway to clear the value if data was sent earlier
            $this->order->update_meta_data('_itglx_wc24_product_hash', '');

            $this->order->save_meta_data();

            $this->order->add_order_note(
                esc_html__('Added deal in CRM #', 'wc-bitrix24-integration')
                . '<a target="_blank" href="'
                . esc_url(Helper::getDealLink($dealID))
                . '">'
                . $dealID
                . '</a>'
            );

            if (
                !isset($settings['not_send_product_list_only_summ'])
                || $settings['not_send_product_list_only_summ'] !== 'true'
            ) {
                $this->setProductRows($dealID, 'deal');
            }

            /**
             * Fires after `deal` by order is created.
             *
             * @since 1.65.0
             *
             * @param int       $dealID
             * @param \WC_Order $order
             */
            \do_action('itglx/wc/bx24/deal-created', $dealID, $this->order);
        }
    }

    private function prepareContactAndCompanyData($sendFields, $orderData)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        $sendFields['customer_id'] = $this->order->get_customer_id();
        $sendFields['contact'] = $this->prepareFields($settings['contact'], $orderData);
        $sendFields['contact']['ADDRESS_COUNTRY'] = \WC()->countries->countries[$this->order->get_billing_country()];

        if ($this->order->get_shipping_country()) {
            $sendFields['contact']['ADDRESS_COUNTRY-11'] = \WC()->countries->countries[$this->order->get_shipping_country()];
        }

        $sendFields['contact']['COMMENTS'] = $this->order->get_customer_note();
        $sendFields['company'] = $this->prepareFields($settings['company'], $orderData);
        $sendFields['company']['ADDRESS_COUNTRY'] = \WC()->countries->countries[$this->order->get_billing_country()];
        $sendFields['company']['COMMENTS'] = $this->order->get_customer_note();

        if (
            isset($settings['contact_update_exists'])
            && (int) $settings['contact_update_exists'] === 1
        ) {
            $sendFields['contact_update_exists'] = true;
        }

        /**
         * Filters a set of contact fields by created order.
         *
         * @since 1.48.0
         *
         * @param array     $contactFields Current array of contact fields.
         * @param \WC_Order $order         Order object.
         */
        $sendFields['contact'] = \apply_filters(
            'wc_bitrix24_plugin_contact_by_order_fields_before_send',
            $sendFields['contact'],
            $this->order
        );

        return $sendFields;
    }

    private function prepareOrderData()
    {
        $data = $this->order->get_data();
        $returnData = [];

        foreach ($data['billing'] as $key => $value) {
            $returnData['billing_' . $key] = $value;
        }

        if (!empty($returnData['billing_country']) && !empty($returnData['billing_state'])) {
            $states = \WC()->countries->get_states($returnData['billing_country']);

            if (isset($states[$returnData['billing_state']])) {
                $returnData['billing_state'] = $states[$returnData['billing_state']];
            }
        }

        foreach ($data['shipping'] as $key => $value) {
            $returnData['shipping_' . $key] = $value;
        }

        if (!empty($returnData['shipping_country']) && !empty($returnData['shipping_state'])) {
            $states = \WC()->countries->get_states($returnData['shipping_country']);

            if (isset($states[$returnData['shipping_state']])) {
                $returnData['shipping_state'] = $states[$returnData['shipping_state']];
            }
        }

        $strposFunc = 'strpos';

        if (function_exists('mb_strpos')) {
            $strposFunc = 'mb_strpos';
        }

        if (!empty($data['meta_data'])) {
            foreach ($data['meta_data'] as $orderMeta) {
                if (!method_exists($orderMeta, 'get_data')) {
                    continue;
                }

                $metaData = $orderMeta->get_data();

                // ignore meta data is object or is array
                if (
                    isset($metaData['value'])
                    && (
                        is_object($metaData['value'])
                        || is_array($metaData['value'])
                    )
                ) {
                    continue;
                }

                if (!empty($metaData['value']) && Helper::isJson($metaData['value'])) {
                    $jsonValue = json_decode($metaData['value'], true);

                    if (!empty($jsonValue['url'])) {
                        $metaData['value'] = $jsonValue['url'];
                    }

                    unset($jsonValue);
                }

                // Supports Saphali Woocommerce Russian
                if (
                    $strposFunc($metaData['key'], '_billing') === 0
                    || $strposFunc($metaData['key'], '_shipping') === 0
                ) {
                    $returnData[mb_substr($metaData['key'], 1)] = $metaData['value'];
                }

                // Supports `Booster for WooCommerce` custom checkout fields
                if ($strposFunc($metaData['key'], 'wcj_checkout_field_') !== false) {
                    $returnData[mb_substr($metaData['key'], 1)] = $metaData['value'];
                } else {
                    // Supports `WC Fields Factory` custom checkout fields
                    if ($strposFunc($metaData['key'], '_billing_') !== false) {
                        $returnData['wcccf' . $metaData['key']] = $metaData['value'];

                        // Supports `WooCommerce Checkout Manager`
                        if (defined('WOOCCM_PLUGIN_NAME')) {
                            $returnData[mb_substr($metaData['key'], 1)] = $metaData['value'];
                        }
                    }

                    // Supports `WC Fields Factory` custom checkout fields
                    if ($strposFunc($metaData['key'], '_shipping_') !== false) {
                        $returnData['wcccf' . $metaData['key']] = $metaData['value'];

                        // Supports `WooCommerce Checkout Manager`
                        if (defined('WOOCCM_PLUGIN_NAME')) {
                            $returnData[mb_substr($metaData['key'], 1)] = $metaData['value'];
                        }
                    }
                }

                $returnData[$metaData['key']] = $metaData['value'];
            }
        }

        // Supports used coupon list
        $returnData['order_coupon_list'] = '';

        // method WC 3.7+
        if (method_exists($this->order, 'get_coupon_codes')) {
            if (!empty($this->order->get_coupon_codes())) {
                $returnData['order_coupon_list'] = implode(', ', $this->order->get_coupon_codes());
            }
        }
        // method before WC 3.7+
        elseif (!empty($this->order->get_used_coupons())) {
            $returnData['order_coupon_list'] = implode(', ', $this->order->get_used_coupons());
        }

        // Supports fo Dokan vendor plugin
        if (class_exists('\Dokan_Vendor')) {
            $vendor = get_post_field('post_author', $this->order->get_id(), 'raw');
            $vendorName = get_userdata($vendor)->display_name;

            $returnData['dokan_vendor'] = $vendorName;
        }

        // Supports for WC Marketplace vendor plugin
        if (class_exists('\WCMp')) {
            $vendorName = '';
            $vendor = $this->order->get_meta('_vendor_id', true);

            if ($vendor) {
                $vendor = get_wcmp_vendor($vendor);

                if ($vendor) {
                    $vendorName = $vendor->page_title;
                }
            } else {
                $vendor = [];

                foreach ($this->order->get_items() as $item) {
                    $vendor[] = wc_get_order_item_meta($item->get_id(), 'Sold By', true);
                }

                if ($vendor) {
                    $vendor = array_unique($vendor);
                    $vendorName = implode(', ', $vendor);
                }
            }

            $returnData['wc_marketplace_vendor'] = $vendorName;
        } else {
            $returnData['wc_marketplace_vendor'] = '';
        }

        // Generate name product attribute by first product
        $attributes = \wc_get_attribute_taxonomies();

        $orderItems = $this->order->get_items();

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $attributeName = 'wciwtb_' . $attribute->attribute_name;

                if (!empty($orderItems)) {
                    $firstItem = array_shift($orderItems);

                    $name = \wc_get_product_terms(
                        $firstItem->get_product_id(),
                        'pa_' . $attribute->attribute_name,
                        ['fields' => 'names']
                    );

                    if (!empty($name)) {
                        $returnData[$attributeName] = array_shift($name);
                    } else {
                        $returnData[$attributeName] = '';
                    }
                } else {
                    $returnData[$attributeName] = '';
                }
            }
        }

        $orderItems = $this->order->get_items();

        // get voucher code by plugin `WooCommerce - PDF Vouchers`
        if (defined('WOO_VOU_META_PREFIX')) {
            if (!empty($orderItems)) {
                $firstItem = array_shift($orderItems);
                $codesItemMeta = wc_get_order_item_meta($firstItem->get_id(), WOO_VOU_META_PREFIX . 'codes');
                $returnData['voucher_code'] = $codesItemMeta;
            } else {
                $returnData['voucher_code'] = '';
            }
        }

        // support Atum
        if (class_exists('Atum\Inc\Helpers')) {
            $summ = 0;

            foreach ($this->order->get_items() as $item) {
                /** @psalm-suppress UndefinedClass */
                Globals::enable_atum_product_data_models();
                $product = wc_get_product(!empty($item['variation_id']) ? $item['variation_id'] : $item['product_id']);
                /** @psalm-suppress UndefinedClass */
                Globals::disable_atum_product_data_models();

                $price = $product->get_purchase_price();

                if ($price) {
                    $summ += (float) $item->get_quantity() * (float) $price;
                }
            }

            $returnData['atum_items_purchase_amount'] = $summ;
        }

        $orderItems = $this->order->get_items();

        // support use first product title in fields
        if (!empty($orderItems)) {
            $firstItem = array_shift($orderItems);

            $returnData['first_product_title'] = $firstItem->get_name();
        } else {
            $returnData['first_product_title'] = '';
        }

        // support use admin link to order
        $returnData['order_edit_admin_link'] = Helper::getOrderLink($this->order, false);

        $skuList = [];
        $totalWeight = 0;

        foreach ($this->order->get_items() as $item) {
            if (version_compare(WC_VERSION, '4.4', '<')) {
                $product = $this->order->get_product_from_item($item);
            } else {
                $product = $item->get_product();
            }

            if ($product instanceof \WC_Product) {
                if ($product->get_weight() > 0) {
                    $totalWeight += $item->get_quantity() * $product->get_weight();
                }

                if ($product->get_sku()) {
                    $skuList[] = $product->get_sku();
                }
            }
        }

        if ($totalWeight) {
            $returnData['order_total_weight'] = $totalWeight;
        } else {
            $returnData['order_total_weight'] = '';
        }

        if ($skuList) {
            $returnData['order_product_sku_list'] = implode(', ', $skuList);
        } else {
            $returnData['order_product_sku_list'] = '';
        }

        $returnData = AdditionalOptionsDataPreparer::prepare($returnData, $this->order);
        $returnData = OrderUserStatDataPreparer::prepare($returnData, $this->order);

        $returnData['order_id'] = $this->order->get_id();

        return OrderMetaDataPreparer::prepare($returnData, $this->order);
    }

    private function prepareFields($fields, $orderData)
    {
        foreach ($orderData as $key => $value) {
            foreach ($fields as $keyField => $fieldValue) {
                if ($key == $fieldValue) {
                    $fields[$keyField] = $value;
                }
            }
        }

        // Resolving populate fields
        $temporaryFields = $fields;

        foreach ($fields as $keyField => $fieldValue) {
            if (isset($fields[$keyField . '-populate'])) {
                if (!empty($fields[$keyField . '-populate'])) {
                    $temporaryFields[$keyField] = $fields[$keyField . '-populate'];
                }

                unset($temporaryFields[$keyField . '-populate']);
            }
        }

        // clearing empty fields
        foreach ($fields as $keyField => $fieldValue) {
            if (is_array($fieldValue)) {
                continue;
            }

            if (strpos($fieldValue, 'billing_') !== false) {
                if (isset($keyField)) {
                    unset($temporaryFields[$keyField]);
                }
            }

            if (strpos($fieldValue, 'shipping_') !== false) {
                if (isset($keyField)) {
                    unset($temporaryFields[$keyField]);
                }
            }
        }

        return $temporaryFields;
    }

    /**
     * @return array
     */
    private function generateProductRows(): array
    {
        $productRows = [];
        $taxIncluded = 'N';

        if (get_option('woocommerce_calc_taxes') == 'yes'
            && get_option('woocommerce_prices_include_tax') == 'yes'
        ) {
            $taxIncluded = 'Y';
        }

        $sendTaxInfo = apply_filters('wc_bitrix24_plugin_send_tax_info', true);

        foreach ($this->order->get_items(['line_item', 'fee']) as $item) {
            $bitrixProductID = (int) $this->getBitrixProductID($item);

            $price = round($item->get_total() / $item->get_quantity(), wc_get_price_decimals());
            $taxItem = round($this->order->get_item_tax($item), wc_get_price_decimals());

            if (abs($taxItem) > 0) {
                $taxPercent = round($taxItem / ($price / 100), 1);
            } else {
                $taxPercent = 0;
            }

            if ($sendTaxInfo) {
                $price += $taxItem;
            }

            $discount = 0;

            if (
                $item->get_type() !== 'fee'
                && $item->get_subtotal() !== $item->get_total()
            ) {
                $discount = $item->get_subtotal() - $item->get_total();
            }

            $productRow = [
                'PRODUCT_ID' => $bitrixProductID,
                'PRICE' => $price,
                'DISCOUNT_SUM' => $discount / $item->get_quantity(),
                'TAX_INCLUDED' => $sendTaxInfo ? $taxIncluded : '',
                'TAX_RATE' => $sendTaxInfo ? $taxPercent : '',
                'QUANTITY' => $item->get_quantity(),
            ];

            if (empty($bitrixProductID)) {
                $productRow['PRODUCT_NAME'] = $item->get_name();
            }

            $productRows[] = $productRow;
        }

        // Supports shipping method
        if (
            \apply_filters('wc_bitrix24_plugin_send_shipping_as_order_item', true)
            && $this->order->get_shipping_method()
        ) {
            $bitrixProduct = Crm::sendApiRequest(
                'crm.product.list',
                false,
                [
                    'FILTER' => [
                        'NAME' => esc_html__('Shipping', 'wc-bitrix24-integration')
                            . ' - '
                            . $this->order->get_shipping_method(),
                    ],
                    'SELECT' => ['ID'],
                ]
            );

            if (!$bitrixProduct) {
                $bitrixProduct = Crm::sendApiRequest(
                    'crm.product.add',
                    false,
                    [
                        'fields' => [
                            'NAME' => esc_html__('Shipping', 'wc-bitrix24-integration')
                                . ' - '
                                . $this->order->get_shipping_method(),
                            'CURRENCY_ID' => $this->order->get_currency(),
                        ],
                    ]
                );

                $bitrixProduct = current($bitrixProduct);
            } else {
                $bitrixProduct = $bitrixProduct[0]['ID'];
            }

            $price = round($this->order->get_shipping_total(), wc_get_price_decimals());
            $shippingTax = round($this->order->get_shipping_tax(), wc_get_price_decimals());

            if ($shippingTax > 0) {
                $taxPercent = $shippingTax / ($price / 100);
            } else {
                $taxPercent = 0;
            }

            if ($sendTaxInfo) {
                $price += $shippingTax;
            }

            $productRow = [
                'PRODUCT_ID' => $bitrixProduct,
                'PRICE' => $price,
                'TAX_INCLUDED' => $sendTaxInfo ? $taxIncluded : '',
                'TAX_RATE' => $sendTaxInfo ? $taxPercent : '',
                'QUANTITY' => 1,
            ];

            if (empty($bitrixProduct)) {
                $productRow['PRODUCT_NAME'] = esc_html__('Shipping', 'wc-bitrix24-integration')
                    . ' - '
                    . $this->order->get_shipping_method();
            }

            $productRows[] = $productRow;
        }

        /**
         * Filters the list of product items that will be added to the lead/deal.
         *
         * @since 1.67.0
         *
         * @param array     $productRows
         * @param \WC_Order $order
         */
        return \apply_filters('itglx/wc/bx24/product-rows', $productRows, $this->order);
    }

    /**
     * @return string
     */
    private function generateNote(): string
    {
        $noteContent = '';

        if (apply_filters('wc_bitrix24_plugin_comment_include_additional_product_data', true)) {
            foreach ($this->order->get_items() as $itemID => $item) {
                ob_start();
                do_action('woocommerce_order_item_meta_start', $itemID, $item, $this->order, true);

                wc_display_item_meta($item);

                do_action('woocommerce_order_item_meta_end', $itemID, $item, $this->order, true);

                $content = ob_get_clean();

                if (!empty($content)) {
                    $noteContent .= esc_html__('Product', 'wc-bitrix24-integration')
                        . '<strong> - '
                        . wp_kses_post(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, false))
                        . '</strong><br>';

                    $noteContent .= esc_html__('Additional info:', 'wc-bitrix24-integration')
                        . '<br>';

                    $noteContent .= $content;
                }
            }
        }

        if (!empty($noteContent)) {
            $noteContent = '<br><br>' . $noteContent;
        }

        $noteContent = $this->order->get_customer_note() . $noteContent;

        return apply_filters('wc_bitrix24_plugin_comment_content', $noteContent, $this->order);
    }

    /**
     * @param array  $orderData
     * @param string $title
     *
     * @return string
     */
    private function generateTitle(array $orderData, string $title): string
    {
        $orderData['order_number'] = $this->order->get_order_number();
        $orderData['order_date'] = $this->order->get_date_created()->date_i18n('d.m.Y');

        $keys = array_map(function ($key) {
            return '{' . $key . '}';
        }, array_keys($orderData));
        $values = array_values($orderData);
        array_walk($values, function (&$value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
        });

        return trim(str_replace($keys, $values, $title));
    }

    private function getBitrixProductID($item)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);
        $postID = false;
        $bitrixProductID = false;

        if ($item->is_type('line_item')) {
            $postID = $item['variation_id'] ?: $item['product_id'];
            $bitrixProductID = get_post_meta($postID, '_itglx_bitrix24_id', true);
        }

        if ($bitrixProductID) {
            Helper::log(
                'product/variation - '
                . ($item['variation_id'] ? $item['product_id'] . '/' . $item['variation_id'] : $item['product_id'])
                . ' has bx24 id - '
                . $bitrixProductID
            );

            // check exists
            if (!empty($settings['check_exists_product_if_has_link_by_id'])) {
                $result = Crm::sendApiRequest(
                    'crm.product.get',
                    false,
                    [
                        'id' => $bitrixProductID,
                    ]
                );

                if (!empty($result)) {
                    return $bitrixProductID;
                }
            } else {
                return $bitrixProductID;
            }
        }

        if ($postID) {
            $xmlID = get_post_meta($postID, '_id_1c', true);
            $xmlID = apply_filters('wc_bitrix24_plugin_order_item_xml_id', $xmlID, $item, $this->order);
        } else {
            $xmlID = false;
        }

        if ($xmlID) {
            $bitrixProduct = Crm::sendApiRequest(
                'crm.product.list',
                false,
                [
                    'FILTER' => ['XML_ID' => $xmlID],
                    'SELECT' => ['ID'],
                ]
            );

            if ($bitrixProduct) {
                $bitrixProductID = $bitrixProduct[0]['ID'];

                if ($postID) {
                    update_post_meta($postID, '_itglx_bitrix24_id', $bitrixProductID);
                }

                return $bitrixProductID;
            }
        }

        $itemName = $item->get_name();
        $price = '';

        if ($item->is_type('line_item')) {
            if (version_compare(WC_VERSION, '4.4', '<')) {
                $product = $this->order->get_product_from_item($item);
            } else {
                $product = $item->get_product();
            }

            if (
                empty($settings['do_not_add_sku_in_product_name'])
                && $product instanceof \WC_Product
                && $product->get_sku()
            ) {
                $itemName .= ' / '
                    . esc_html__('SKU', 'wc-bitrix24-integration')
                    . ': '
                    . $product->get_sku();
            }

            if ($product instanceof \WC_Product) {
                $price = $product->get_regular_price();
            }
        }

        /**
         * Filters a set of parameters when searching for an existing product.
         *
         * @since 1.56.0
         *
         * @param array          $filterParams Default: ['NAME' => $itemName].
         * @param \WC_Order_Item $item
         * @param \WC_Order      $order
         */
        $filterParams = \apply_filters('itglx/wc/bx24/find-product-filter-args', ['NAME' => $itemName], $item, $this->order);

        $bitrixProduct = Crm::sendApiRequest(
            'crm.product.list',
            false,
            [
                'FILTER' => $filterParams,
                'SELECT' => ['ID'],
            ]
        );

        if ($bitrixProduct) {
            $bitrixProductID = $bitrixProduct[0]['ID'];

            if ($postID) {
                update_post_meta($postID, '_itglx_bitrix24_id', $bitrixProductID);
            }

            return $bitrixProductID;
        }

        $newProductFields = [
            'NAME' => $itemName,
            'CURRENCY_ID' => $this->order->get_currency(),
            'XML_ID' => $xmlID,
            'PRICE' => $price,
        ];

        if (!empty($settings['product_create_send_image'])) {
            $thumbnailId = \get_post_meta($item['product_id'], '_thumbnail_id', true);

            if (is_numeric($thumbnailId)) {
                $path = \get_attached_file($thumbnailId);

                if (file_exists($path)) {
                    $newProductFields['DETAIL_PICTURE'] = [
                        'fileData' => [
                            basename($path),
                            base64_encode(file_get_contents($path)),
                        ],
                    ];
                }
            }
        }

        /**
         * Filters a set of fields for a new product in Bitrix24.
         *
         * @since 1.60.0
         *
         * @param array          $newProductFields
         * @param \WC_Order_Item $item
         * @param \WC_Order      $order
         *
         * @see https://training.bitrix24.com/rest_help/crm/products/crm_product_add.php
         */
        $newProductFields = \apply_filters('itglx/wc/bx24/add-product-fields', $newProductFields, $item, $this->order);

        $bitrixProduct = Crm::sendApiRequest('crm.product.add', false, ['fields' => $newProductFields]);

        $bitrixProductID = current($bitrixProduct);

        if ($postID) {
            update_post_meta($postID, '_itglx_bitrix24_id', $bitrixProductID);
        }

        return $bitrixProductID;
    }

    /**
     * @param int    $entryID Bitrix24 entry ID.
     * @param string $type    Entry type - `lead` or `deal`
     *
     * @return void
     *
     * @see https://dev.1c-bitrix.ru/rest_help/crm/leads/crm_lead_productrows_set.php
     * @see https://dev.1c-bitrix.ru/rest_help/crm/cdeals/crm_deal_productrows_set.php
     */
    private function setProductRows($entryID, $type)
    {
        $productRows = $this->generateProductRows();
        $hash = md5(json_encode($productRows));

        if ($hash === $this->order->get_meta('_itglx_wc24_product_hash', true)) {
            Helper::log('list of items unchanged - ignore - ' . $this->order->get_id());

            return;
        }

        Crm::sendApiRequest('crm.' . $type . '.productrows.set', false, ['id' => $entryID, 'rows' => $productRows]);

        $this->order->update_meta_data('_itglx_wc24_product_hash', $hash);
        $this->order->save_meta_data();
    }
}
