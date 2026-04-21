<?php

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Crm;

if (!defined('ABSPATH')) {
    exit;
}

if (function_exists('itglxWcbx24UpdateDealStageAssociatedWithOrder')) {
    return;
}

function itglxWcbx24UpdateDealStageAssociatedWithOrder($orderID, $dealStage)
{
    $order = wc_get_order($orderID);
    $dealID = $order->get_meta('_wc_bitrix24_deal_id', true);

    if (!$dealID) {
        return;
    }

    Crm::sendApiRequest(
        'crm.deal.update',
        false,
        [
            'id' => $dealID,
            'fields' => [
                'STAGE_ID' => $dealStage,
            ],
        ]
    );
}
