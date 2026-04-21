<?
global $xs_data, $wpdb, $big_data;

if(empty($big_data) || count($big_data) == 0)
{
	$big_data['not_cache'] = 'y';	
	include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
}

$order_item_id = isset($_POST['order_item_id']) ? (int)xs_format($_POST['order_item_id']) : $xs_data['order_item_id'];
$price_total = 0;
$sale_price_total = 0;

$db_components = get_store_components_order($order_item_id, true);

?><div class="store_components xs_ajax_load xs_flex xs_wrap xs_start"><?
	
	if(count($db_components) > 0)
	{
		foreach($db_components as $v)
		{
			?><div class="item active"><?
				
				?><div class="xs_quantity_set"><?
				
					for($i = 0; $i <= 5; $i++)
					{
						?><div class="xs_quantity_set__item" onclick="
							var q = jQuery(this).parents('.item').find('.xs_quantity input')
							q.val((<?=$i ?> ? parseInt(q.val()) + <?=$i ?> : 0))
							set_quantity(q, (<?=$i ?> ? 300 : 0))
						"><?=$i ?></div><?
					}
					
				?></div><?
				
				?><div class="xs_quantity xs_flex" data-order_item_id="<?=$order_item_id ?>" data-component_id="<?=$v->component_id ?>"><?
					?><span class="minus"></span><?
					?><input type="text" value="<?=$v->quantity ? $v->quantity : 0 ?>" /><?
					?><span class="plus"></span><?
				?></div><?
				
				if($v->favorite == 'y')
				{
					?><span class="xs_set_favorite favorite xs_flex xs_middle xs_center active"><? get_svg("star", $v->color) ?></span><?
				}
				
				?><div class="link"><?
					?><span class="image"<?=!empty($v->image) ? ' style="background-image:url('.$big_data['component_image_path'].$v->image.')"' : "" ?>></span><?
					?><a href="/wp-admin/admin.php?page=store&section=detail&id=<?=$v->component_id ?>" class="name_container" target="_blank"><?=$v->name ?></a><?
					?><span class="xs_flex pq_container"><?
						?><span class="price<?=$v->price <= 0 ? ' xs_red' : ""?>"><?=wc_price($v->price, ['decimals' => 2]) ?></span><?
						
						if($v->sale_price > 0)
						{
							?><span class="sale_price"><?=wc_price($v->sale_price, ['decimals' => 2]) ?></span><?
						}
						
						/*
						?><span class="group_days days_<?=$v->days ?> group_<?=$v->group_id ?>"><?
							?><span class="group"><?=get_store_group_name($v->group_id) ?></span>&nbsp;/&nbsp;<? 
							?><span class="days"><?=$v->days ?>&nbsp;дн</span><?
						?></span><?
						*/
						
					?></span><?
				?></div><?
			?></div><?
			
			$price_total = $price_total + ($v->price * $v->quantity);
			
			if($v->sale_price == 0)
				$v->sale_price = $v->price;
			
			$sale_price_total = $sale_price_total + ($v->sale_price * $v->quantity);
		}
	}
	
	?><div class="item add"><?
		?><a href="/wp-content/themes/xsiteshop/load/xs_select_components.php?order_item_id=<?=$order_item_id ?>" data-type="ajax" class="fancybox link xs_flex xs_middle xs_center"><span class="name_container xs_flex xs_center xs_middle"><span class="name">Добавить компонент</span></span></a><?
	?></div><?
	
	if(count($db_components))
	{
		?><p class="store_components__total">
			Итого <strong><?=count($db_components)."</strong> ".format_by_count(count($db_components), "компонент", "компонента", "компонентов") ?> 
			на сумму <strong><?=wc_price($price_total) ?></strong><?
		
			if($sale_price_total > $price_total)
			{
				?> &nbsp;&nbsp;<span style="text-decoration: line-through;"><?=wc_price($sale_price_total) ?></span><?
			}
			
		?></p><?
	}

?></div><?