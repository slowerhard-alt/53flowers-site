<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options;

class FullNameOption
{
    /**
     * @param array     $returnData
     * @param \WC_order $order
     *
     * @return array
     */
    public static function prepare($returnData, $order)
    {
        $returnData['billing_full_name'] = $order->get_billing_last_name() . ' ' . $order->get_billing_first_name();
        $returnData['billing_full_name_backward'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $returnData['shipping_full_name'] = $order->get_shipping_last_name() . ' ' . $order->get_shipping_first_name();
        $returnData['shipping_full_name_backward'] = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();

        return $returnData;
    }
}
