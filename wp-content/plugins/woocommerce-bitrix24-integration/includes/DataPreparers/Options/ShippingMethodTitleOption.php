<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options;

class ShippingMethodTitleOption
{
    /**
     * @param \WC_Order $order
     *
     * @return string
     */
    public static function prepare($order)
    {
        if ($order->get_shipping_method()) {
            return $order->get_shipping_method();
        }

        return '';
    }
}
