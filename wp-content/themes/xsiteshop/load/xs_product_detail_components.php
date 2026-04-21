<?

global $big_data;

$product_id = 0;

if(empty($big_data) || count($big_data) == 0)
{
	$big_data['not_cache'] = 'y';
	include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
	$product_id = (int)xs_format($_POST['product_id']);
}
else
{
	global $product;
	$product_id = $product->get_id();
}

if($product_id)
{
	$components = get_store_components_product($product_id, true);

	if($components && count($components) > 0)
	{
		foreach($components as $v)
		{
			//pre($v);
			
			if($v->not_filter != 'y')
			{
				?><li>
					<span><?=$v->name ?></span>
					<strong><?=$v->quantity ?> шт.</strong>
				</li><?
			}
		}
	}
}