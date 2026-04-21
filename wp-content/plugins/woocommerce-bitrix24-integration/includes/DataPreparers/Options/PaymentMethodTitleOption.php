<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options;

class PaymentMethodTitleOption
{
    /**
     * @param \WC_Order $order
     *
     * @return string
     */
    public static function prepare($order)
    {
        if (wc_get_payment_gateway_by_order($order)) {
            return wc_get_payment_gateway_by_order($order)->title;
        }

        return '';
    }
}
