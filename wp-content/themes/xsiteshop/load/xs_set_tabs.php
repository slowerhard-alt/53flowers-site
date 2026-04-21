<?
global $big_data;

$big_data['not_cache'] = 'y';

include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

if(!is_user_logged_in())
	die();

$product_id = (int)xs_format($_GET['product_id']);
$is_admin = xs_format($_GET['is_admin']);

if($is_admin == 'y')
{
	?><div class="xs_admin_tags"><?
		
		xs_meta_data_tabs(false);
		
		?><input type="submit" class="button action" value="Применить"><?
	?></div><?
}