<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

global $wpdb, $big_data;

$component_id = (int)xs_format($_POST['component_id']);
$order_item_id = (int)xs_format($_POST['order_item_id']);
$quantity = (float)xs_format($_POST['quantity']);

if($component_id == 0 || $order_item_id == 0 || !isset($_POST['quantity']))
	die();

set_store_orders($order_item_id, $component_id, $quantity);