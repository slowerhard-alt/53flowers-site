<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\ProductVariation;

class Bitrix24IdFieldVariation
{
    public function __construct()
    {
        \add_action('woocommerce_product_after_variable_attributes', [$this, 'field'], 10, 3);
        \add_action('woocommerce_save_product_variation', [$this, 'save'], 10, 1);
    }

    /**
     * @param int      $i
     * @param array    $variationData
     * @param \WP_Post $variation
     */
    public function field($i, $variationData, \WP_Post $variation)
    {
        echo '<div>';

        \woocommerce_wp_text_input(
            [
                'id' => 'variation_bitrix24_id[' . \esc_attr($variation->ID) . ']',
                'label' => \esc_html__('product ID (Bitrix24)', 'wc-bitrix24-integration'),
                'value' => \esc_attr(\get_post_meta($variation->ID, '_itglx_bitrix24_id', true)),
                'type' => 'number',
            ]
        );

        echo '</div>';
    }

    /**
     * @param int $variationId
     */
    public function save($variationId)
    {
        if (!isset($_POST['product_id']) || !isset($_POST['variation_bitrix24_id'])) {
            return;
        }

        if (!isset($_POST['variation_bitrix24_id'][$variationId])) {
            return;
        }

        \update_post_meta($variationId, '_itglx_bitrix24_id', \wp_unslash($_POST['variation_bitrix24_id'][$variationId]));
    }
}
