<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options\CustomerUserOption;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options\FullNameOption;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options\OrderProductTitlesListOption;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options\PaymentMethodTitleOption;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options\ShippingMethodTitleOption;

class AdditionalOptionsDataPreparer
{
    /**
     * @param array     $returnData
     * @param \WC_order $order
     *
     * @return array
     */
    public static function prepare($returnData, $order)
    {
        $returnData = FullNameOption::prepare($returnData, $order);
        $returnData = CustomerUserOption::prepare($returnData, $order->get_customer_id());
        $returnData['order_product_titles_list'] = OrderProductTitlesListOption::prepare($order);
        $returnData['shipping_method_title'] = ShippingMethodTitleOption::prepare($order);
        $returnData['payment_method_title'] = PaymentMethodTitleOption::prepare($order);

        return $returnData;
    }
}
