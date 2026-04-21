<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\SendActions;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Crm;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;

class ProductUpdateSendAction
{
    /**
     * Hook name.
     *
     * @var string
     */
    public static $name = 'itglx/wc/bx24/send_product_update_to_crm';

    private static $instance = false;

    protected function __construct()
    {
        \add_action(self::$name, [$this, 'action']);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param int $productId
     *
     * @return void
     */
    public function action($productId)
    {
        Helper::log('[product/cron] process send update - ' . $productId);

        $bitrix24ProductId = get_post_meta($productId, '_itglx_bitrix24_id', true);

        if (empty($bitrix24ProductId)) {
            return;
        }

        $product = wc_get_product($productId);

        Crm::sendApiRequest(
            'crm.product.update',
            false,
            [
                'id' => $bitrix24ProductId,
                'fields' => [
                    'NAME' => $product->get_title(),
                ],
            ]
        );
    }
}
