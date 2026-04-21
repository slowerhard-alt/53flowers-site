<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;

class WcBulkOrderToCrm
{
    public function __construct()
    {
        if (!Helper::isEnabled()) {
            return;
        }

        add_filter('bulk_actions-edit-shop_order', [$this, 'addItemInActionList'], 10, 1);
        add_filter('handle_bulk_actions-edit-shop_order', [$this, 'handleAction'], 10, 3);

        // HPOS
        add_filter('bulk_actions-woocommerce_page_wc-orders', [$this, 'addItemInActionList'], 10, 1);
        add_filter('handle_bulk_actions-woocommerce_page_wc-orders', [$this, 'handleAction'], 10, 3);

        add_action('admin_notices', [$this, 'adminNotice']);
    }

    /**
     * @param array $actions
     *
     * @return array
     */
    public function addItemInActionList(array $actions): array
    {
        $actions['send_order_to_bitrix24'] = esc_html__(
            'Send to Bitrix24',
            'wc-bitrix24-integration'
        );

        return $actions;
    }

    /**
     * @param string $redirectTo
     * @param string $action
     * @param array  $ids
     *
     * @return string
     */
    public function handleAction(string $redirectTo, string $action, array $ids): string
    {
        if ($action !== 'send_order_to_bitrix24') {
            return $redirectTo;
        }

        $orderIds = get_option('bx_wc_bulk_order_sent_to_crm', []);
        $orderIds = array_merge((array) $orderIds, array_map('absint', $ids));
        $orderIds = array_unique($orderIds);

        update_option('bx_wc_bulk_order_sent_to_crm', $orderIds);

        if (!empty($orderIds)) {
            if (!\as_next_scheduled_action(Bootstrap::CRON_TASK_BULK_ORDERS)) {
                \as_schedule_recurring_action(time(), 60, Bootstrap::CRON_TASK_BULK_ORDERS);

                Helper::log('register recurring action to bulk send');
            } else {
                Helper::log('recurring action already registered');
            }
        }

        // HPOS
        if (isset($_GET['page']) && $_GET['page'] == 'wc-orders') {
            $redirectTo = add_query_arg(
                [
                    'send' => count($ids),
                    'bulk_action' => 'send_order_to_bitrix24',
                ],
                $redirectTo
            );
        } else {
            $redirectTo = add_query_arg(
                [
                    'post_type' => 'shop_order',
                    'send' => count($ids),
                    'bulk_action' => 'send_order_to_bitrix24',
                ],
                $redirectTo
            );
        }

        Helper::log('register bulk send ' . count($orderIds) . ' orders', $orderIds);

        return esc_url_raw($redirectTo);
    }

    /**
     * @return void
     */
    public function adminNotice(): void
    {
        if (!isset($_GET['bulk_action']) || $_GET['bulk_action'] !== 'send_order_to_bitrix24') {
            return;
        }

        $number = isset($_GET['send']) ? (int) $_GET['send'] : 0;

        $message = sprintf(
            esc_html__('%s orders registered for sending to CRM, the dispatch time depends on the number of orders.', 'wc-bitrix24-integration'),
            number_format_i18n($number)
        );

        echo '<div class="updated"><p>' . $message . '</p></div>';
    }
}
