<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\Actions;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\SendActions\ProductUpdateSendAction;

class ProductUpdateAction
{
    private static $instance = false;

    protected function __construct()
    {
        if (!$this->isEnabled()) {
            return;
        }

        ProductUpdateSendAction::getInstance();

        \add_action('woocommerce_update_product', [$this, 'action'], PHP_INT_MAX);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param int         $productId
     * @param \WC_Product $product
     *
     * @return void
     */
    public function action(int $productId)
    {
        // once execute
        if (\did_action('woocommerce_update_product') > 1) {
            return;
        }

        $bitrix24ProductId = get_post_meta($productId, '_itglx_bitrix24_id', true);

        if (empty($bitrix24ProductId)) {
            return;
        }

        if (\as_next_scheduled_action(ProductUpdateSendAction::$name, [$productId])) {
            Helper::log('[product] there is a pending update event - ' . $productId);

            return;
        }

        Helper::log('[product] register update - ' . $productId);

        // register one time task
        \as_schedule_single_action(time(), ProductUpdateSendAction::$name, [$productId]);
    }

    /**
     * @return bool
     */
    private function isEnabled(): bool
    {
        if (!Helper::isVerify()) {
            return false;
        }

        $settings = \get_option(Bootstrap::OPTIONS_KEY, []);

        return Helper::isEnabled()
            && !empty($settings['update_product_in_crm']);
    }
}
