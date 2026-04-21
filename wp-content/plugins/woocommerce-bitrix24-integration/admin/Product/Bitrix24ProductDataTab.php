<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\Product;

class Bitrix24ProductDataTab
{
    public function __construct()
    {
        \add_filter('woocommerce_product_data_tabs', [$this, 'addTab'], 10, 1);
        \add_action('woocommerce_product_data_panels', [$this, 'tabContent']);
        \add_action('woocommerce_process_product_meta', [$this, 'tabContentSave'], 10, 1);
    }

    /**
     * @param array $tabs
     *
     * @return array
     */
    public function addTab($tabs)
    {
        $tabs['itgalaxy-woocommerce-bitrix24-product-id'] = [
            'label' => \esc_html__('Bitrix24', 'wc-bitrix24-integration'),
            'target' => 'itgalaxy-woocommerce-bitrix24-product-id',
        ];

        return $tabs;
    }

    /**
     * @return void
     */
    public function tabContent()
    {
        ?>
        <div id="itgalaxy-woocommerce-bitrix24-product-id" class="panel woocommerce_options_panel">
        <?php
        \woocommerce_wp_text_input(
            [
                'id' => 'product_bitrix24_id',
                'value' => \esc_attr(\get_post_meta(\get_the_ID(), '_itglx_bitrix24_id', true)),
                'label' => \esc_html__('product ID', 'wc-bitrix24-integration'),
                'type' => 'number',
            ]
        ); ?>
        <hr class="show_if_variable">
        <p class="show_if_variable">
            <strong>
                <?php \esc_html_e('Each variation has its own field with information about ID.', 'wc-bitrix24-integration'); ?>
            </strong>
        </p>
        </div>
        <?php
    }

    /**
     * @param int $postID
     *
     * @return void
     */
    public function tabContentSave($postID)
    {
        if (!isset($_POST['product_bitrix24_id'])) {
            return;
        }

        \update_post_meta($postID, '_itglx_bitrix24_id', \wp_unslash($_POST['product_bitrix24_id']));
    }
}
