<?
global $wpdb;

// Получаем 10 случайных WooCommerce-товаров у которых есть компоненты
$xs_check_products = $wpdb->get_results("
    SELECT DISTINCT
        p.product_id,
        po.post_title AS product_name,
        pm_price.meta_value AS actual_price
    FROM xsite_store_products p
    INNER JOIN xsite_posts po ON po.ID = p.product_id AND po.post_type = 'product' AND po.post_status = 'publish'
    INNER JOIN xsite_postmeta pm_price ON pm_price.post_id = p.product_id AND pm_price.meta_key = '_price'
    WHERE p.site = '53flowers'
    ORDER BY RAND()
    LIMIT 10
");

// Для каждого товара получаем компоненты
$xs_check_data = array();

foreach ($xs_check_products as $product) {
    $components = $wpdb->get_results("
        SELECT
            c.id,
            c.name,
            c.price,
            c.forced_price,
            c.original_price,
            p.quantity
        FROM xsite_store_products p
        INNER JOIN xsite_store_components c ON c.id = p.component_id
        WHERE p.product_id = '" . (int)$product->product_id . "'
        AND p.site = '53flowers'
        ORDER BY c.sort, c.id
    ");

    $calc_price_sum = 0;
    foreach ($components as $comp) {
        $use_price = $comp->forced_price > 0 ? $comp->forced_price : $comp->price;
        $calc_price_sum += $use_price * $comp->quantity;
    }

    $calc_price = $calc_price_sum > 0 ? ceil($calc_price_sum / 5) * 5 : 0;
    $actual_price = (float)$product->actual_price;
    $diff = abs($actual_price - $calc_price);

    $xs_check_data[] = array(
        'product_id'   => (int)$product->product_id,
        'product_name' => $product->product_name,
        'actual_price' => $actual_price,
        'calc_price'   => $calc_price,
        'diff'         => $diff,
        'components'   => $components,
    );
}
