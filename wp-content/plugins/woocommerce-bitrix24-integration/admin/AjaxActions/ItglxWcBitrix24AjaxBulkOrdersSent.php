<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;

class ItglxWcBitrix24AjaxBulkOrdersSent
{
    public function __construct()
    {
        add_action('wp_ajax_itglxWcBitrix24AjaxBulkOrdersSent', [$this, 'actionProcessing']);
    }

    public function actionProcessing()
    {
        if (!current_user_can('manage_options')) {
            exit;
        }

        if (!Helper::isEnabled()) {
            $this->errorRegisterResponse(
                \esc_html__(
                    'The webhook is not specified or the main checkbox is not enabled.',
                    'wc-bitrix24-integration'
                )
            );
        }

        $type = !empty($_POST['type']) ? $_POST['type'] : 'all';

        $query = [
            'type' => 'shop_order',
            'status' => 'any',
            'orderby' => 'date',
            'order' => 'ASC',
            'limit' => -1,
            'return' => 'ids',
        ];

        if ($type === 'all') {
            $orders = \wc_get_orders($query);

            if (!is_wp_error($orders)) {
                $this->successRegisterResponse($orders);
            } else {
                $this->errorRegisterResponse('error wc_get_orders');
            }
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        if ($settings['type'] === 'lead') {
            $metaKey = '_wc_bitrix24_lead_id';
        } elseif ($settings['type'] === 'contact_only') {
            $metaKey = '_wc_bx24_order_sent';
        } else {
            $metaKey = '_wc_bitrix24_deal_id';
        }

        $query['meta_key'] = $metaKey;
        $query['meta_compare'] = 'NOT EXISTS';

        $orders = \wc_get_orders($query);

        if (!is_wp_error($orders)) {
            $this->successRegisterResponse($orders);
        } else {
            $this->errorRegisterResponse('error wc_get_orders');
        }
    }

    private function successRegisterResponse($orders)
    {
        update_option('bx_wc_bulk_order_sent_to_crm', $orders);

        if (!empty($orders)) {
            if (!\as_next_scheduled_action(Bootstrap::CRON_TASK_BULK_ORDERS)) {
                \as_schedule_recurring_action(time(), 60, Bootstrap::CRON_TASK_BULK_ORDERS);

                Helper::log('[ajax] register recurring action to bulk send');
            } else {
                Helper::log('[ajax] recurring action already registered');
            }
        }

        Helper::log('[ajax] register bulk send ' . count($orders) . ' orders', $orders);

        \wp_send_json_success(
            [
                'message' => sprintf(
                    esc_html__('%s orders registered for sending to CRM, the dispatch time depends on the number of orders.', 'wc-bitrix24-integration'),
                    number_format_i18n(count($orders))
                ),
            ]
        );
    }

    private function errorRegisterResponse($message)
    {
        \wp_send_json_error(
            [
                'message' => \esc_html($message),
            ]
        );
    }
}
