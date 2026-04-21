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
		
		xs_product_product_top_category(false);
		
		?><input type="submit" class="button action" value="Применить"><?
	?></div><?
}
else
{
	if($product_id == 0)
		die();

	if(!$post = get_post($product_id))
		die();

	if(isset($_POST['product_id']) && !empty($_POST['product_id']) && (int)$_POST['product_id'] > 0)
	{
		if(isset($_POST['top_category']))
			update_post_meta((int)$_POST['product_id'], 'top_category', xs_format($_POST['top_category']));
		else
			delete_post_meta((int)$_POST['product_id'], 'top_category');	
	}

	?><form class="xs_set_top"><?

		xs_product_product_top_category($post);
		
		?><input type="hidden" name="product_id" value="<?=$product_id ?>" />
		<a class="xs_set_top__update_link" href="#" onclick="window.location.reload(true);return false;">Сохранить и обновить страницу</a>
	</form><?
}