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

$component_id = (int)xs_format($_POST['component_id']);
$product_id = (int)xs_format($_POST['product_id']);
$quantity = (float)xs_format($_POST['quantity']);
$name = isset($_POST['name']) 
	? xs_format($_POST['name'])
	: "";
$sale = isset($_POST['sale']) 
	? (int)$_POST['sale']
	: 0;
$is_markup = isset($_POST['is_markup']) && $_POST['is_markup'] == 'y'
	? 'y'
	: '';

if($component_id == 0 || $product_id == 0 || !isset($_POST['quantity']))
	die();

set_store_products($product_id, $component_id, $quantity, $name, $sale, $is_markup);
