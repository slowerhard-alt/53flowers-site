<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;

class OrderMetaDataPreparer
{
    /**
     * @param array     $returnData
     * @param \WC_Order $order
     *
     * @return array
     */
    public static function prepare(array $returnData, \WC_Order $order): array
    {
        $rawMeta = Helper::getMetaKeys();

        if (empty($rawMeta)) {
            return $returnData;
        }

        if (!Helper::HPOSEnabled()) {
            $returnData = self::noHPOS($rawMeta, $returnData, $order);
        } else {
            $returnData = self::withHPOS($rawMeta, $returnData, $order);
        }

        return $returnData;
    }

    /**
     * @param array     $rawMeta
     * @param array     $returnData
     * @param \WC_Order $order
     *
     * @return array
     */
    private static function noHPOS(array $rawMeta, array $returnData, \WC_Order $order): array
    {
        $currentOrderMeta = \get_post_meta($order->get_id());

        foreach ($rawMeta as $metaKey) {
            // set the value only if there is no current
            if (!empty($returnData[$metaKey])) {
                continue;
            }

            if (!empty($currentOrderMeta[$metaKey])) {
                $returnData[$metaKey] = reset($currentOrderMeta[$metaKey]);

                if (!empty($returnData[$metaKey]) && Helper::isJson($returnData[$metaKey])) {
                    $jsonValue = json_decode($returnData[$metaKey], true);

                    if (!empty($jsonValue['url'])) {
                        $returnData[$metaKey] = $jsonValue['url'];
                    }

                    unset($jsonValue);
                }
            } else {
                $returnData[$metaKey] = '';
            }
        }

        return $returnData;
    }

    /**
     * @param array     $rawMeta
     * @param array     $returnData
     * @param \WC_Order $order
     *
     * @return array
     */
    private static function withHPOS(array $rawMeta, array $returnData, \WC_Order $order): array
    {
        // prepare for all keys
        foreach ($rawMeta as $metaKey) {
            // set the value only if there is no current
            if (!empty($returnData[$metaKey])) {
                continue;
            }

            $returnData[$metaKey] = '';
        }

        $currentOrderMeta = $order->get_meta_data();

        foreach ($currentOrderMeta as $orderMeta) {
            if (!method_exists($orderMeta, 'get_data')) {
                continue;
            }

            $metaData = $orderMeta->get_data();

            // set the value only if there is no current
            if (!empty($returnData[$metaData['key']])) {
                continue;
            }

            if (
                empty($metaData['value'])
                || is_object($metaData['value'])
                || is_array($metaData['value'])
            ) {
                continue;
            }

            $returnData[$metaData['key']] = $metaData['value'];

            if (Helper::isJson($metaData['value'])) {
                $jsonValue = json_decode($metaData['value'], true);

                if (!empty($jsonValue['url'])) {
                    $returnData[$metaData['key']] = $jsonValue['url'];
                }

                unset($jsonValue);
            }
        }

        return $returnData;
    }
}
