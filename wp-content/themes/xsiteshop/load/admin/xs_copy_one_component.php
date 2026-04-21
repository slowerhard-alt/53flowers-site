<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

global $wpdb, $big_data;

$variation_id = (int)xs_format($_POST['variation_id']);
$copy_to = xs_format($_POST['copy_to']);

if($variation_id == 0 || !is_array($copy_to) || !count($copy_to))
	die();

if(!$product = get_post($variation_id))
	die();

$ar_components = get_store_components_product($variation_id, true);

foreach($copy_to as $v)
{
	if(!$product_to = get_post($v))
		continue;

	clear_store_products($product_to->ID);

	if($ar_components)
	{
		foreach($ar_components as $_v)
			set_store_products($product_to->ID, $_v->component_id, $_v->quantity, $_v->name_in_product, $_v->sale_in_product, $_v->is_markup, $_v->is_set_stock_ms);
	}
}
