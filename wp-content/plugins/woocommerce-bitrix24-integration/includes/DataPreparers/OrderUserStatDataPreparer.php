<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers;

use Itgalaxy\PluginCommon\AnalyticsHelper;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;

class OrderUserStatDataPreparer
{
    /**
     * @var string
     */
    const META_KEY = '_wc_bitrix24_user_stat_data';

    /**
     * @var string[]
     */
    const UTM_FIELD_LIST = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
    ];

    /**
     * @param int $orderID
     */
    public static function save($orderID)
    {
        $order = \wc_get_order($orderID);
        /**
         * If the data has already been written, then there is no need to overwrite and change them, since the values
         * should remain in their original state.
         */
        if (!empty($order->get_meta(self::META_KEY, true))) {
            return;
        }

        $order->update_meta_data(
            self::META_KEY,
            [
                'utm' => Helper::parseUtmCookie(),
                'gaClientID' => AnalyticsHelper::getGetGaClientIdFromCookie(),
                'roistat_visit' => AnalyticsHelper::getCookieRoistatVisit(),
                'yandexClientID' => AnalyticsHelper::getCookieYandexClientId(),
                '_fbp' => $_COOKIE['_fbp'] ?? '',
                '_fbc' => $_COOKIE['_fbc'] ?? '',
            ]
        );

        $order->save_meta_data();
    }

    /**
     * @param array     $returnData
     * @param \WC_Order $order
     *
     * @return array
     */
    public static function prepare($returnData, $order)
    {
        $orderUserStatData = $order->get_meta(self::META_KEY, true);

        if (!is_array($orderUserStatData)) {
            $orderUserStatData = [];
        }

        // the default values are empty
        foreach (self::UTM_FIELD_LIST as $field) {
            $returnData[$field] = '';
        }

        // if there is data on tags
        if (!empty($orderUserStatData['utm'])) {
            $utmFields = $orderUserStatData['utm'];

            foreach (self::UTM_FIELD_LIST as $field) {
                /**
                 * We use `rawurldecode`, since the value may have contained characters that were encoded for
                 * transmission in the link, so we need to decode them.
                 *
                 * Example: encoded '%D1%81%D0%BB%D0%BE%D0%B2%D0%BE' = decoded `слово`
                 */
                $returnData[$field] = !empty($utmFields[$field]) ? rawurldecode(\wp_unslash($utmFields[$field])) : '';
            }
        }

        $returnData['roistat_visit'] = $orderUserStatData['roistat_visit'] ?? '';
        $returnData['gaClientID'] = $orderUserStatData['gaClientID'] ?? '';
        $returnData['yandexClientID'] = $orderUserStatData['yandexClientID'] ?? '';
        $returnData['_fbp'] = $orderUserStatData['_fbp'] ?? '';
        $returnData['_fbc'] = $orderUserStatData['_fbc'] ?? '';

        return $returnData;
    }
}
