<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options;

class CustomerUserOption
{
    /**
     * @param array $returnData
     * @param int   $customerUserId
     *
     * @return array
     */
    public static function prepare($returnData, $customerUserId)
    {
        $returnData['customer_user_id'] = '';
        $returnData['customer_username'] = '';
        $returnData['customer_date_created'] = '';
        $returnData['customer_role'] = '';
        $returnData['customer_total_orders'] = '';
        $returnData['customer_total_spent'] = '';
        $returnData['customer_last_order_date'] = '';

        if (empty($customerUserId)) {
            return $returnData;
        }

        $customer = new \WC_Customer($customerUserId);

        $returnData['customer_user_id'] = $customer->get_id();
        $returnData['customer_username'] = $customer->get_username();
        $returnData['customer_role'] = $customer->get_role();
        $returnData['customer_total_orders'] = $customer->get_order_count();
        $returnData['customer_total_spent'] = $customer->get_total_spent();

        if ($customer->get_date_created()) {
            $returnData['customer_date_created'] = $customer->get_date_created()->date_i18n('Y-m-d');
        }

        if ($customer->get_last_order()) {
            $returnData['customer_last_order_date'] = $customer->get_last_order()->get_date_created()->date_i18n('Y-m-d');
        }

        return $returnData;
    }
}
