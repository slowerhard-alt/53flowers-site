<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions;

use Automattic\WooCommerce\Utilities\OrderUtil;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;

class ItglxWcBitrix24AjaxRemoveLinksWithOrders
{
    public function __construct()
    {
        add_action('wp_ajax_itglxWcBitrix24AjaxRemoveLinksWithOrders', [$this, 'actionProcessing']);
    }

    public function actionProcessing()
    {
        if (!current_user_can('manage_options')) {
            exit;
        }

        global $wpdb;

        $wpdb->delete($wpdb->postmeta, ['meta_key' => '_wc_bx24_order_sent']);
        $wpdb->delete($wpdb->postmeta, ['meta_key' => '_wc_bx24_order_has_try_fix_contact']);
        $wpdb->delete($wpdb->postmeta, ['meta_key' => '_wc_bitrix24_lead_id']);
        $wpdb->delete($wpdb->postmeta, ['meta_key' => '_wc_bitrix24_deal_id']);

        if (Helper::HPOSEnabled()) {
            $tableName = OrderUtil::get_table_for_order_meta();

            $wpdb->delete($tableName, ['meta_key' => '_wc_bx24_order_sent']);
            $wpdb->delete($tableName, ['meta_key' => '_wc_bx24_order_has_try_fix_contact']);
            $wpdb->delete($tableName, ['meta_key' => '_wc_bitrix24_lead_id']);
            $wpdb->delete($tableName, ['meta_key' => '_wc_bitrix24_deal_id']);
        }

        \wp_send_json_success(
            [
                'message' => \esc_html__('Links successfully removed.', 'wc-bitrix24-integration'),
            ]
        );
    }
}
