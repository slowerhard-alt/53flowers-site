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
		
		xs_product_tag(false);
		
		?><input type="submit" class="button action" value="Применить"><?
	?></div><?
}
else
{
	if($product_id == 0)
		die();

	if(!$post = get_post($product_id))
		die();

	if(isset($_POST['tax_input']) && is_array($_POST['tax_input']) && count($_POST['tax_input']) > 0)
	{
		foreach($_POST['tax_input'] as $k => $v)
		{
			foreach($v as $_k => $_v)
				if($_v == '0')
					unset($v[$_k]);
			
			wp_set_post_terms($product_id, $v, $k);
		}
	}

	?><form class="xs_set_tags"><?

		xs_product_tag($post);
		
		?><input type="hidden" name="product_id" value="<?=$product_id ?>" />
		<a class="xs_set_top__update_link" href="#" onclick="window.location.reload(true);return false;">Сохранить и обновить страницу</a>
	</form><?
}