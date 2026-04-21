<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes;

use Automattic\WooCommerce\Utilities\OrderUtil;

class Helper
{
    public static function log($message, $data = [], $type = 'info')
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);
        $enableLogging = isset($settings['enabled_logging']) && (int) $settings['enabled_logging'] === 1;

        if (!$enableLogging) {
            return;
        }

        try {
            Bootstrap::$common->logger->log('wcbx24', $message, (array) $data, $type);
        } catch (\Exception $exception) {
            // Nothing
        }
    }

    public static function isEnabled()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        return !empty($settings['enabled'])
            && (int) $settings['enabled'] === 1
            && !empty($settings['webhook']);
    }

    public static function isVerify()
    {
        $value = get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY);

        if (!empty($value)) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getMetaKeys(): array
    {
        global $wpdb;

        // not HPOS
        if (!self::HPOSEnabled()) {
            $metaKeysArray = $wpdb->get_results(
                "SELECT `meta`.`meta_key`
                FROM `{$wpdb->posts}` as `posts`
                INNER JOIN `{$wpdb->postmeta}` as `meta` ON (`meta`.`post_id` = `posts`.`ID`)
                WHERE `posts`.`post_type` = 'shop_order'
                AND `meta`.`meta_key` != ''
                GROUP BY `meta`.`meta_key`",
                'ARRAY_A'
            );
        } else {
            $tableName = OrderUtil::get_table_for_order_meta();

            $metaKeysArray = $wpdb->get_results(
                "SELECT `meta_key` FROM `{$tableName}` WHERE `meta_key` != '' GROUP BY `meta_key`",
                'ARRAY_A'
            );
        }

        $metaKeys = [];

        if ($metaKeysArray) {
            foreach ($metaKeysArray as $meta) {
                $metaKeys[] = $meta['meta_key'];
            }
        }

        return $metaKeys;
    }

    public static function getLeadLink($leadID)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);
        $webhook = $settings['webhook'];
        $startLink = explode('rest', $webhook);

        return $startLink[0] . 'crm/lead/details/' . $leadID . '/';
    }

    public static function getDealLink($dealID)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);
        $webhook = $settings['webhook'];
        $startLink = explode('rest', $webhook);

        return $startLink[0] . 'crm/deal/details/' . $dealID . '/';
    }

    public static function getOrderLink($order, $html = true)
    {
        $link = $order->get_edit_order_url();

        if (!$html) {
            return $link;
        }

        return '<a href="' . esc_url($link) . '">' . esc_html($link) . '</a>';
    }

    public static function isJson($string)
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }

    public static function parseUtmCookie()
    {
        if (!empty($_COOKIE[Bootstrap::UTM_COOKIES])) {
            return json_decode(wp_unslash($_COOKIE[Bootstrap::UTM_COOKIES]), true);
        }

        return [];
    }

    public static function resolveNextResponsible($list)
    {
        $list = explode(',', $list);

        if (count($list) === 1) {
            return $list[0];
        }

        $last = get_option('_wc_b24_last_responsible', 0);
        $lastKey = array_search($last, $list);

        if (empty($last) || $lastKey === false || ($lastKey + 1) >= count($list)) {
            update_option('_wc_b24_last_responsible', $list[0]);

            return $list[0];
        }

        update_option('_wc_b24_last_responsible', $list[$lastKey + 1]);

        return $list[$lastKey + 1];
    }

    public static function getProductIdByMeta($value, $metaKey = '_id_1c', $isVariation = false)
    {
        global $wpdb;

        $product = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT `meta`.`post_id` as `post_id`, `posts`.`post_type` as `post_type` FROM `{$wpdb->postmeta}` as `meta`
                INNER JOIN `{$wpdb->posts}` as `posts` ON (`meta`.`post_id` = `posts`.`ID`)
                WHERE `meta`.`meta_value` = %s AND `meta`.`meta_key` = %s",
                (string) $value,
                (string) $metaKey
            )
        );

        if (!isset($product->post_type)) {
            return null;
        }

        if ($isVariation) {
            return $product->post_type === 'product_variation' ? $product->post_id : null;
        }

        return $product->post_type === 'product' ? $product->post_id : null;
    }

    /**
     * @return bool
     */
    public static function HPOSEnabled(): bool
    {
        if (!class_exists('\Automattic\WooCommerce\Utilities\OrderUtil')) {
            return false;
        }

        return OrderUtil::custom_orders_table_usage_is_enabled();
    }
}
