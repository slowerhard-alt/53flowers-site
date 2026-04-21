<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

global $wpdb, $big_data;

$product_id = (int)xs_format($_POST['product_id']);

if(!$product_id || !is_user_logged_in())
	die();

$ar_products = [$product_id];

if($child_variations = get_posts(['numberposts' => -1, 'post_type' => 'product_variation', 'post_parent' => $product_id]))
	foreach($child_variations as $v)
		$ar_products[] = $v->ID;

if(count($ar_products))
{
	foreach($ar_products as $id)
	{
		delete_post_meta($id, 'sale_in_product');
		delete_post_meta($id, 'is_markup');
	}

	$wpdb->query("
		UPDATE 
			`xsite_store_products` 
		SET 
			`sale_in_product` = '0', 
			`is_markup` = 'n' 
		WHERE 
			`product_id` IN ('".implode("','", $ar_products)."') AND 
			`site` = '53flowers'
	");
}