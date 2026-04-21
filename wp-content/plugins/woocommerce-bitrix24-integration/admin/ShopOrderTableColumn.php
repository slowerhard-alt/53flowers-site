<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;

class ShopOrderTableColumn
{
    public function __construct()
    {
        \add_filter('manage_edit-shop_order_columns', [$this, 'addColumn'], 10, 1);
        \add_action('manage_shop_order_posts_custom_column', [$this, 'addValue'], 10, 2);

        // HPOS
        \add_filter('woocommerce_shop_order_list_table_columns', [$this, 'addColumn'], 10, 1);
        \add_action('woocommerce_shop_order_list_table_custom_column', [$this, 'addValue'], 10, 2);
    }

    /**
     * @param array $columns
     *
     * @return array
     */
    public function addColumn(array $columns): array
    {
        $columns['bx24_value'] = esc_html__('Bitrix24', 'wc-bitrix24-integration');

        return $columns;
    }

    /**
     * @param string        $columnName
     * @param int|\WC_Order $order
     *
     * @return void
     */
    public function addValue(string $columnName, $order): void
    {
        if ($columnName !== 'bx24_value') {
            return;
        }

        $settings = \get_option(Bootstrap::OPTIONS_KEY, []);

        if (empty($settings['type'])) {
            return;
        }

        $order = is_numeric($order) ? \wc_get_order($order) : $order;

        if ($settings['type'] === 'lead') {
            $id = $order->get_meta('_wc_bitrix24_lead_id', true);

            echo $id
                ? '<strong>'
                    . esc_html__('lead ID: ', 'wc-bitrix24-integration')
                    . '</strong>'
                    . esc_html($id)
                : esc_html__('no data', 'wc-bitrix24-integration');
        } else {
            $id = $order->get_meta('_wc_bitrix24_deal_id', true);

            echo $id
                ? '<strong>'
                    . esc_html__('deal ID: ', 'wc-bitrix24-integration')
                    . '</strong>'
                    . esc_html($id)
                : esc_html__('no data', 'wc-bitrix24-integration');
        }
    }
}
