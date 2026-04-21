<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes;

class Cron
{
    public function __construct()
    {
        add_action('init', [$this, 'createCron']);
        add_action(Bootstrap::CRON_TASK_SEND, [$this, 'singleSentCron'], 10, 1);
        \add_action(Bootstrap::CRON_TASK_BULK_ORDERS, [$this, 'bulkSentCron']);

        // not bind if run not cron mode
        if (!defined('DOING_CRON') || !DOING_CRON) {
            return;
        }

        add_action(Bootstrap::CRON, [$this, 'cronAction']);
    }

    public function createCron()
    {
        if (!wp_next_scheduled(Bootstrap::CRON)) {
            wp_schedule_event(time(), 'weekly', Bootstrap::CRON);
        }

        if (\wp_next_scheduled('bx_wc_bulk_order_sent_to_crm')) {
            \wp_clear_scheduled_hook('bx_wc_bulk_order_sent_to_crm');
        }

        // unschedule recurring action to bulk send
        if (
            empty(\get_option('bx_wc_bulk_order_sent_to_crm', []))
            && \as_next_scheduled_action(Bootstrap::CRON_TASK_BULK_ORDERS)
        ) {
            \as_unschedule_all_actions(Bootstrap::CRON_TASK_BULK_ORDERS);

            Helper::log('no orders to bulk send, unschedule recurring action');
        }
    }

    public function cronAction()
    {
        $last = get_option(Bootstrap::CRON);

        if ($last === date_i18n('Y-m-d')) {
            return;
        }

        update_option(Bootstrap::CRON, date_i18n('Y-m-d'));

        $response = Bootstrap::$common->requester->call('cron_code_check');

        if (is_wp_error($response)) {
            return;
        }

        if ($response->status === 'stop') {
            update_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, '');
        }
    }

    public function bulkSentCron()
    {
        $orderIds = get_option('bx_wc_bulk_order_sent_to_crm', []);

        if (empty($orderIds)) {
            return;
        }

        Helper::log('[cron/order] process bulk send');

        $countPerRound = \apply_filters('wc_bitrix24_plugin_bulk_order_sent_count_per_round', 3);

        for ($count = 1; $count <= $countPerRound; ++$count) {
            if (count($orderIds) <= 0) {
                return;
            }

            $id = array_shift($orderIds);

            if (!\as_next_scheduled_action(Bootstrap::CRON_TASK_SEND, [$id])) {
                Helper::log('[cron/order] register sent order through bulk send - ' . $id);
                \as_schedule_single_action(time(), Bootstrap::CRON_TASK_SEND, [$id]);
            }

            \update_option('bx_wc_bulk_order_sent_to_crm', $orderIds);
        }
    }

    public function singleSentCron($orderId)
    {
        Helper::log('[cron/order] process send order - ' . $orderId);

        $orderSender = OrderToBitrix24::getInstance();
        $orderSender->orderSendCrm($orderId);
    }
}
