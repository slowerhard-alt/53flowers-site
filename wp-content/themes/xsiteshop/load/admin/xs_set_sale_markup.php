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
$sale_in_product = (int)xs_format($_POST['sale_in_product']);
$is_markup = xs_format($_POST['is_markup']);

update_post_meta($product_id, 'sale_in_product', $sale_in_product);
update_post_meta($product_id, 'is_markup', $is_markup);

set_product_price($product_id);
