<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

if(!is_user_logged_in())
	die();

global $wpdb, $big_data;

$component_id = (int)xs_format($_GET['component_id']);
$current_product_id = (int)xs_format($_GET['current_product_id']);
$product_id = preg_replace('/[^0-9,]/', '', xs_format($_GET['product_id']));

$ar_product_ids = [];
$e = explode(",", $product_id);

foreach($e as $v)
	$ar_product_ids[] = (int)$v;

if($current_product_id == 0)
	$current_product_id = (int)$ar_product_ids[0];

if($component_id == 0 || $current_product_id == 0)
	die();

if($row = $wpdb->get_row("SELECT * FROM `xsite_store_products` WHERE `product_id` = '".$current_product_id."' AND `component_id` = '".$component_id."' AND `site` = '53flowers'"))
{
	if($row->is_set_stock_ms == 'y')
		$wpdb->query("UPDATE `xsite_store_products` SET `is_set_stock_ms` = '' WHERE `product_id` IN ('".implode("','", $ar_product_ids)."') AND `component_id` = '".$component_id."' AND `site` = '53flowers'");
	else
	{
		$wpdb->query("UPDATE `xsite_store_products` SET `is_set_stock_ms` = 'y' WHERE `product_id` IN ('".implode("','", $ar_product_ids)."') AND `component_id` = '".$component_id."' AND `site` = '53flowers'");
		echo "active";
	}
}