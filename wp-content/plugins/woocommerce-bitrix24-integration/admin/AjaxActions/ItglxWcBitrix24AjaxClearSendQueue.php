<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;

class ItglxWcBitrix24AjaxClearSendQueue
{
    public function __construct()
    {
        add_action('wp_ajax_itglxWcBitrix24AjaxClearSendQueue', [$this, 'actionProcessing']);
    }

    public function actionProcessing()
    {
        if (!current_user_can('manage_options')) {
            exit;
        }

        \update_option('bx_wc_bulk_order_sent_to_crm', []);
        \as_unschedule_all_actions(Bootstrap::CRON_TASK_SEND);
        \as_unschedule_all_actions(Bootstrap::CRON_TASK_BULK_ORDERS);

        Helper::log('[ajax] clear send queue');

        \wp_send_json_success(
            [
                'message' => \esc_html__('The send queue was cleared successfully.', 'wc-bitrix24-integration'),
            ]
        );
    }
}
