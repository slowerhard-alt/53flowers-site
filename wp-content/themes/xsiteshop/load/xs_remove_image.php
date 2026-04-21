<?

include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
global $big_data, $wpdb;

if( isset( $_GET['deletefiles'] ) )
{
	if($image = $wpdb->get_var("SELECT `image` FROM `xsite_store_components` WHERE `id` = '".(int)$_GET['component_id']."'"))
	{
		if(!empty($image))
		{
			unlink($_SERVER['DOCUMENT_ROOT'].$big_data['component_image_path'].$image);
			$wpdb->query("UPDATE `xsite_store_components` SET `image` = '' WHERE `id` = '".(int)$_GET['component_id']."'");	
		}
	}
}