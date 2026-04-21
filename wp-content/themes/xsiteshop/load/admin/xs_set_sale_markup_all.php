<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

global $wpdb, $big_data;

$product_id = (int)xs_format($_POST['product_id']);
$sale_in_product = (int)xs_format($_POST['sale_in_product']);
$is_markup = xs_format($_POST['is_markup']);

update_post_meta($product_id, 'sale_in_product', $sale_in_product);
update_post_meta($product_id, 'is_markup', $is_markup);

$ar_products[] = $product_id;

if($child_variations = get_posts(['numberposts' => -1, 'post_type' => 'product_variation', 'post_parent' => $product_id]))
{
	$wpdb->query("DELETE FROM `xsite_store_products` WHERE `product_id` = '".$product_id."' AND `site` = '53flowers'");
	
	foreach($child_variations as $v)
		$ar_products[] = $v->ID;
}

foreach($ar_products as $v)
	set_product_price($v);
