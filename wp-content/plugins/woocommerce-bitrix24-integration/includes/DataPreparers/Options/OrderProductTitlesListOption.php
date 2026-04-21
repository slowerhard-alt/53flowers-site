<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options;

class OrderProductTitlesListOption
{
    /**
     * @param \WC_Order $order
     *
     * @return string
     */
    public static function prepare($order)
    {
        $list = [];

        foreach ($order->get_items() as $item) {
            $list[] = $item->get_name();
        }

        return implode(', ', $list);
    }
}
