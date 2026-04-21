<?
$product_id = isset($_GET['product_id'])
	? (int)xs_format($_GET['product_id'])
	: 0;
	
$site = isset($_GET['site']) && $_GET['site'] == "cvetyru-vn"
	? xs_format($_GET['site'])
	: "53flowers";

$xs_data['product_id'] = 1;

$wpdb->query("DELETE FROM `xsite_store_products` WHERE `product_id` = '".$xs_data['product_id']."'");

update_post_meta($xs_data['product_id'], 'sale_in_product', '');
update_post_meta($xs_data['product_id'], 'is_markup', '');

if(!$product_id)
{
	set_store_products($xs_data['product_id'], 671, 1);
	set_store_products($xs_data['product_id'], 673, 1);
	set_store_products($xs_data['product_id'], 676, 1);
	set_store_products($xs_data['product_id'], 665, 1);
}
else
{
	if($ar_components = get_store_components_product($product_id, false, "c.`sort`, c.`id`", $site))
		foreach($ar_components as $k => $v)
			set_store_products($xs_data['product_id'], $k, $v);
}
