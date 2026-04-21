<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

global $wpdb, $big_data;

if(!current_user_can('administrator'))
{
	header("HTTP/1.1 403 Forbidden");
	die();
}

if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'xs_admin_action'))
{
	header("HTTP/1.1 403 Forbidden");
	die("Invalid nonce");
}

$product_id = (int)xs_format($_POST['product_id']);

if($product_id == 0)
	die();

if(!$product = get_post($product_id))
	die();

$products = get_posts([
	'post_parent' => $product_id,
	'post_type' => 'product_variation',
	'posts_per_page' => -1
]);

if($products)
{
	foreach($products as $v)
		clear_store_products($v->ID);
}

clear_store_products($product_id);
