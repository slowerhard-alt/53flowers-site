<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\Filters;

class WooCommerceDuplicateProductExcludeMeta
{
    public function __construct()
    {
        \add_filter('woocommerce_duplicate_product_exclude_meta', [$this, 'filter'], 10, 1);
    }

    /**
     * @param array $excludeMetaKeys
     *
     * @return array
     */
    public function filter(array $excludeMetaKeys): array
    {
        // ignore link with bitrix24 product
        $excludeMetaKeys[] = '_itglx_bitrix24_id';

        return $excludeMetaKeys;
    }
}
