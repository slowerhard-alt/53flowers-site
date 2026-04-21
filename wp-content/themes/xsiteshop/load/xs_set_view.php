<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

if(!is_user_logged_in())
	die();

global $wpdb, $big_data;

$id = (int)xs_format($_GET['component_id']);

if($id == 0)
	die();
	
if($wpdb->get_var("SELECT `show_in_product` FROM `xsite_store_components` WHERE `id` = '".$id."'") == 'y')
	$wpdb->query("UPDATE `xsite_store_components` SET `show_in_product` = '' WHERE `id` = '".$id."'");
else
{
	$wpdb->query("UPDATE `xsite_store_components` SET `show_in_product` = 'y' WHERE `id` = '".$id."'");
	echo "active";
}