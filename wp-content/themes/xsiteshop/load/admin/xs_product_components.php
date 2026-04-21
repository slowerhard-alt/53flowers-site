<?
global $xs_data, $wpdb, $big_data;

if(empty($big_data) || count($big_data) == 0)
{
	$big_data['not_cache'] = 'y';	
	include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
}

$product_id = isset($_POST['product_id']) ? (int)xs_format($_POST['product_id']) : $xs_data['product_id'];
$parent_id = $wpdb->get_var("SELECT `post_parent` FROM `xsite_posts` WHERE `ID` = '".$product_id."'");
$purchase_price = 0;
$original_price = 0;
$total_price = 0;
$sale_price_total = 0;

if($sale_in_product = (int)$wpdb->get_var("SELECT `meta_value` FROM `xsite_postmeta` WHERE `post_id` = '".$product_id."' AND `meta_key` = 'sale_in_product'"))
	$is_markup = $wpdb->get_var("SELECT `meta_value` FROM `xsite_postmeta` WHERE `post_id` = '".$product_id."' AND `meta_key` = 'is_markup'");
elseif($parent_id)
	if($sale_in_product = (int)$wpdb->get_var("SELECT `meta_value` FROM `xsite_postmeta` WHERE `post_id` = '".$parent_id."' AND `meta_key` = 'sale_in_product'"))
		$is_markup = $wpdb->get_var("SELECT `meta_value` FROM `xsite_postmeta` WHERE `post_id` = '".$parent_id."' AND `meta_key` = 'is_markup'");

$db_components = get_store_components_product($product_id, true);

?><div class="component_table"><?
	
	$_is_markup = get_post_meta($product_id, "is_markup", true);
	$_sale_in_product = get_post_meta($product_id, "sale_in_product", true);
	
	?><div class="store_components__sale-variation" data-product_id="<?=$product_id ?>"><?	
		?><div class="component_table__nav-name">Общая скидка/наценка на вариацию:</div><?
		?><div class="component_table__sale component_table__sale--inline"><?
			?><div class="component_table__nav-sale"><?
				?><input class="component_table__nav-input store_components__nav-input" id="component_table__nav-input-plus-<?=$product_id ?>" type="radio" name="component_table_is_markup[<?=$product_id ?>]" type="radio" value="y"<?=($_is_markup == 'y') ? ' checked' : "" ?> /><?
				?><label class="component_table__nav-label component_table__nav-label--plus" for="component_table__nav-input-plus-<?=$product_id ?>">+</label><?
				?><input class="component_table__nav-input store_components__nav-input" id="component_table__nav-input-minus-<?=$product_id ?>" type="radio" name="component_table_is_markup[<?=$product_id ?>]" type="radio" value="n"<?=($_is_markup != 'y') ? ' checked' : "" ?> /><? 
				?><label class="component_table__nav-label component_table__nav-label--minus" for="component_table__nav-input-minus-<?=$product_id ?>">&minus;</label><?
			?></div><?					
			?><input type="text" class="component_table__set_sale store_components__set_sale" value="<?=($_sale_in_product > 0) ? $_sale_in_product : "" ?>" name="component_table_sale_in_product[<?=$product_id ?>]" placeholder="0" /><?
			?>%
		</div>
	</div> 
	
	<table class="component_table__table wp-list-table widefat striped table-view-list posts">
		<tbody><? 
		
		if(is_array($db_components) && count($db_components))
		{
			foreach($db_components as $v)
			{			
				?><tr class="component_table__item item" data-component_id="<?=$v->component_id ?>">
					<td class="component_table__td-image">
						<span class="component_table__image"<?=!empty($v->image) ? ' style="background-image:url('.$big_data['component_image_path'].$v->image.')"' : "" ?>></span>
					</td>
					<td class="component_table__td-name">
						<div class="component_table__name">
							<a href="/wp-admin/admin.php?page=store&section=detail&id=<?=(int)$v->component_id ?>" target="_blank"><?=esc_html($v->name) ?></a><?
							
							if(!$sale_in_product && $v->sale_on_quantity_percent > 0)
							{
								?><span class="component_table__sale_on_quantity"><?=(int)$v->sale_on_quantity_percent ?>%</span><?
							}
							
						?></div><?
						
						if($v->show_in_product == 'y')
						{
							?><input class="component_table__set_name store_components__set_name" type="text" placeholder="<?=esc_attr(!empty($v->name_for_user) ? $v->name_for_user : $v->name) ?>" value="<?=esc_attr($v->name_in_product) ?>" /><?
						}
						
					?></td>
					<td class="component_table__td-quantity">								
						<div class="component_table__quantity_set"><?
						
							for($i = 0; $i <= 5; $i++)
							{
								?><div class="component_table__quantity_set-item" onclick="
									var q = jQuery(this).parents('.component_table__item').find('.component_table__quantity input')
									q.val((<?=$i ?> ? parseInt(q.val()) + <?=$i ?> : 0))
									set_quantity(q, (<?=$i ?> ? 300 : 0))
								"><?=$i ?></div><?
							}
							
						?></div>
						<div class="component_table__quantity xs_quantity" data-product_id="<?=$product_id ?>" data-component_id="<?=$v->component_id ?>"><?
							?><span class="minus"></span><?
							?><input type="text" value="<?=$v->quantity ? $v->quantity : 0 ?>" /><?
							?><span class="plus"></span><?
						?></div>
					</td>
					<td class="component_table__td-sale">
						<span class="component_table__sale"><?
							?><span class="component_table__nav-sale"><?
								?><input class="component_table__nav-input store_components__nav-input" id="component_table__nav-input-plus-<?=$v->component_id ?>_<?=$product_id ?>" type="radio" name="component_table_is_markup_<?=$v->component_id ?>_<?=$product_id ?>" type="radio" value="y"<?=($v->is_markup == 'y') ? ' checked' : "" ?> /><?
								?><label class="component_table__nav-label component_table__nav-label--plus" for="component_table__nav-input-plus-<?=$v->component_id ?>_<?=$product_id ?>">+</label><?
								?><input class="component_table__nav-input store_components__nav-input" id="component_table__nav-input-minus-<?=$v->component_id ?>_<?=$product_id ?>" type="radio" name="component_table_is_markup_<?=$v->component_id ?>_<?=$product_id ?>" type="radio" value=""<?=($v->is_markup != 'y') ? ' checked' : "" ?> /><?
								?><label class="component_table__nav-label component_table__nav-label--minus" for="component_table__nav-input-minus-<?=$v->component_id ?>_<?=$product_id ?>">&minus;</label><?
							?></span><?					
							?><input type="text" class="component_table__set_sale store_components__set_sale" value="<?=($v->sale_in_product > 0) ? $v->sale_in_product : "" ?>" placeholder="0" /><?
							?>%<?
						?></span>				
					</td>
					<td class="component_table__td-view">
						<div class="component_table__set_container">
							<span class="component_table__set component_table__set--view xs_set_view<?=$v->show_in_product == 'y' ? " active" : "" ?>" title="Отображать для покупателей в карточке товара" data-id="<?=$v->component_id ?>"><? get_svg("view", $v->color) ?></span>
						</div>
					</td>
					<td class="component_table__td-stock">
						<div class="component_table__set_container">
							<span class="component_table__set-lock" data-component_id="<?=$v->component_id ?>"><? get_svg("lock") ?></span>
							<span class="component_table__set component_table__set--stock xs_set_stock<?=$v->is_set_stock_ms == 'y' ? " active" : "" ?>" title="Влияет на наличие товара" data-component_id="<?=$v->component_id ?>" data-product_id="<?=$v->product_id ?>" data-current_product_id="<?=$v->product_id ?>"><? get_svg("stock", $v->color) ?></span>
						</div>
					</td>
					<td class="component_table__td-favorite">
						<div class="component_table__set_container">
							<span class="component_table__set component_table__set--favorite xs_set_favorite<?=$v->favorite == 'y' ? " active" : "" ?>" title="Избранное" data-id="<?=$v->component_id ?>"><? get_svg("star", $v->color) ?></span>
						</div>
					</td>		
					<td class="component_table__td-price"><?
							
						if($v->sale_price > 0)
						{
							?><span class="component_table__sale_price"><?=wc_price($v->sale_price, ['decimals' => 2]) ?></span><?
						}
						
						?><span class="component_table__price<?=$v->sale_price > 0 ? ' xs_red' : ""?>"><?=wc_price($v->price, ['decimals' => 2]) ?></span>
					</td>		
					<td class="component_table__td-total">
						<span class="component_table__price<?=$v->sale_price > 0 ? ' xs_red' : ""?>"><?=wc_price($v->price * $v->quantity, ['decimals' => 2]) ?></span>
					</td>		
				</tr><?  
				
				$purchase_price = ($v->purchase_price > 0)
					? $purchase_price + ($v->purchase_price * $v->quantity)
					: $purchase_price + ($v->original_price * $v->quantity);
				
				$original_price = $original_price + ($v->original_price * $v->quantity);
				
				if($sale_in_product)
				{
					if($is_markup == 'y')
					{
						$v->price = $v->_price + ($v->_price / 100 * $sale_in_product);
						$v->sale_price = $v->_price;
					}
					else
					{
						$v->sale_price = $v->_price;
						$v->price = $v->_price - ($v->_price / 100 * $sale_in_product);
					}
				}
				
				$total_price = $total_price + ($v->price * $v->quantity);
				
				if($v->sale_price == 0)
					$v->sale_price = $v->price;
				
				$sale_price_total = $sale_price_total + ($v->sale_price * $v->quantity);
			}
		}
		
		$is_sale = $sale_price_total > $total_price;
		
		?></tbody><?
		
		if(is_array($db_components) && count($db_components))
		{
			?><tfoot>
				<tr>
					<td colspan="6" rowspan="4">
						<a class="component_table__add button fancybox" href="/wp-content/themes/xsiteshop/load/xs_select_components.php?product_id=<?=$product_id ?>" data-type="ajax">Добавить компонент</a>
					</td>
					<td colspan="2">
						<div class="component_table__total-name"><?=$is_sale ? "Итого без скидки" : "Итого" ?>:</div>
					</td>
					<td><strong><?=$is_sale ? wc_price($sale_price_total) : wc_price($total_price) ?></strong></td>
				</tr><?
				
				if($is_sale)
				{
					?><tr>
						<td colspan="2">
							<div class="component_table__total-name">Итого со скидкой:</div>
						</td>
						<td><strong><?=wc_price($total_price) ?></strong></td>
					</tr><?
				}

				?><tr>
					<td colspan="2">
						<div class="component_table__total-name">Себестоимость:</div>
					</td>
					<td><strong><?=wc_price($original_price) ?></strong></td>
				</tr>
				<!--tr>
					<td colspan="2">
						<div class="component_table__total-name">Маржа:</div>
					</td>
					<td><strong><?=wc_price($total_price - $purchase_price) ?></strong></td>
				</tr-->
				
			</tfoot><?
		}
		else
		{
			?><tfoot>
				<tr>
					<td>
						<a class="component_table__add button fancybox" href="/wp-content/themes/xsiteshop/load/xs_select_components.php?product_id=<?=$product_id ?>" data-type="ajax">Добавить компонент</a>
					</td>
				</tr>
			</tfoot><?
		}
		
	?></table>
</div>






<?
/*
global $xs_data, $wpdb, $big_data;

if(empty($big_data) || count($big_data) == 0)
{
	$big_data['not_cache'] = 'y';	
	include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
}

$product_id = isset($_POST['product_id']) ? (int)xs_format($_POST['product_id']) : $xs_data['product_id'];
$parent_id = $wpdb->get_var("SELECT `post_parent` FROM `xsite_posts` WHERE `ID` = '".$product_id."'");
$purchase_price = 0;
$total_price = 0;
$sale_price_total = 0;

if($sale_in_product = (int)$wpdb->get_var("SELECT `meta_value` FROM `xsite_postmeta` WHERE `post_id` = '".$product_id."' AND `meta_key` = 'sale_in_product'"))
	$is_markup = $wpdb->get_var("SELECT `meta_value` FROM `xsite_postmeta` WHERE `post_id` = '".$product_id."' AND `meta_key` = 'is_markup'");
elseif($parent_id)
	if($sale_in_product = (int)$wpdb->get_var("SELECT `meta_value` FROM `xsite_postmeta` WHERE `post_id` = '".$parent_id."' AND `meta_key` = 'sale_in_product'"))
		$is_markup = $wpdb->get_var("SELECT `meta_value` FROM `xsite_postmeta` WHERE `post_id` = '".$parent_id."' AND `meta_key` = 'is_markup'");

$db_components = get_store_components_product($product_id, true);

?><div class="store_components xs_ajax_load xs_flex xs_wrap xs_start"><?
	
	if(is_array($db_components) && count($db_components))
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
				
				?><div class="xs_quantity xs_flex" data-product_id="<?=$product_id ?>" data-component_id="<?=$v->component_id ?>"><?
					?><span class="minus"></span><?
					?><input type="text" value="<?=$v->quantity ? $v->quantity : 0 ?>" /><?
					?><span class="plus"></span><?
				?></div><?
				
				?><span title="Избранное" class="xs_set_favorite favorite xs_flex xs_middle xs_center<?=$v->favorite == 'y' ? " active" : "" ?>" data-id="<?=$v->component_id ?>"><? get_svg("star", $v->color) ?></span><?
							
				?><span title="Отображать для покупателей в карточке товара" class="xs_set_view view xs_flex xs_middle xs_center<?=$v->show_in_product == 'y' ? " active" : "" ?>" data-id="<?=$v->component_id ?>"><? get_svg("view", $v->color) ?></span><?
							
				?><span title="Влияет на наличие товара" class="xs_set_stock stock xs_flex xs_middle xs_center<?=$v->is_set_stock_ms == 'y' ? " active" : "" ?>" data-component_id="<?=$v->component_id ?>" data-product_id="<?=$v->product_id ?>"><? get_svg("stock", $v->color) ?></span><?
				
				?><span class="store_components__sale"><?
					?><span class="store_components__nav-sale"><?
						?><input class="store_components__nav-input" id="store_components__nav-input-plus-<?=$v->component_id ?>_<?=$product_id ?>" type="radio" name="is_markup_<?=$v->component_id ?>_<?=$product_id ?>" type="radio" value="y"<?=($v->is_markup == 'y') ? ' checked' : "" ?> /><?
						?><label class="store_components__nav-label store_components__nav-label--plus" for="store_components__nav-input-plus-<?=$v->component_id ?>_<?=$product_id ?>">+</label><?
						?><input class="store_components__nav-input" id="store_components__nav-input-minus-<?=$v->component_id ?>_<?=$product_id ?>" type="radio" name="is_markup_<?=$v->component_id ?>_<?=$product_id ?>" type="radio" value=""<?=($v->is_markup != 'y') ? ' checked' : "" ?> /><?
						?><label class="store_components__nav-label store_components__nav-label--minus" for="store_components__nav-input-minus-<?=$v->component_id ?>_<?=$product_id ?>">&minus;</label><?
					?></span><?					
					?><input type="text" class="store_components__set_sale" value="<?=($v->sale_in_product > 0) ? $v->sale_in_product : "" ?>" placeholder="0" /><?
					?>%<?
				?></span><?
				
				?><div class="link"><?
					?><span class="image"<?=!empty($v->image) ? ' style="background-image:url('.$big_data['component_image_path'].$v->image.')"' : "" ?>></span><?
					?><a href="/wp-admin/admin.php?page=store&section=detail&id=<?=$v->component_id ?>" class="name_container" target="_blank"><?=$v->name ?></a><?
					
					if($v->show_in_product == 'y')
					{
						?><input type="text" class="store_components__set_name" placeholder="<?=!empty($v->name_for_user) ? $v->name_for_user : $v->name ?>" value="<?=$v->name_in_product ?>" /><?
					}
					
					?><span class="xs_flex pq_container"><?
						?><span class="price<?=$v->price <= 0 ? ' xs_red' : ""?>"><?=wc_price($v->price, ['decimals' => 2]) ?></span><?
						
						if($v->sale_price > 0)
						{
							?><span class="sale_price"><?=wc_price($v->sale_price, ['decimals' => 2]) ?></span><?
						}
						
						?><span class="price<?=$v->price <= 0 ? ' xs_red' : ""?>"><?=wc_price($v->price * $v->quantity, ['decimals' => 2]) ?></span><?

					?></span><?
				?></div><?
			?></div><?
			
			$purchase_price = ($v->purchase_price > 0)
				? $purchase_price + ($v->purchase_price * $v->quantity)
				: $purchase_price + ($v->original_price * $v->quantity);
			
			if($sale_in_product)
			{
				if($is_markup == 'y')
				{
					$v->price = $v->_price + ($v->_price / 100 * $sale_in_product);
					$v->sale_price = $v->_price;
				}
				else
				{
					$v->sale_price = $v->_price;
					$v->price = $v->_price - ($v->_price / 100 * $sale_in_product);
				}
			}
			
			$total_price = $total_price + ($v->price * $v->quantity);
			
			if($v->sale_price == 0)
				$v->sale_price = $v->price;
			
			$sale_price_total = $sale_price_total + ($v->sale_price * $v->quantity);
		}
	}
	
	?><div class="item add"><?
		?><a href="/wp-content/themes/xsiteshop/load/xs_select_components.php?product_id=<?=$product_id ?>" data-type="ajax" class="fancybox link xs_flex xs_middle xs_center"><span class="name_container xs_flex xs_center xs_middle"><span class="name">Добавить компонент</span></span></a><?
	?></div><?
	
	$is_markup = get_post_meta($product_id, "is_markup", true);
	$sale_in_product = get_post_meta($product_id, "sale_in_product", true);
	
	?><div class="store_components__sale-variation" data-product_id="<?=$product_id ?>"><?	
		?><div class="store_components__nav-name">Общая скидка/наценка на вариацию:</div><?
		?><div class="store_components__sale store_components__sale--inline"><?
			?><div class="store_components__nav-sale"><?
				?><input class="store_components__nav-input" id="store_components__nav-input-plus-<?=$product_id ?>" type="radio" name="is_markup[<?=$product_id ?>]" type="radio" value="y"<?=($is_markup == 'y') ? ' checked' : "" ?> /><?
				?><label class="store_components__nav-label store_components__nav-label--plus" for="store_components__nav-input-plus-<?=$product_id ?>">+</label><?
				?><input class="store_components__nav-input" id="store_components__nav-input-minus-<?=$product_id ?>" type="radio" name="is_markup[<?=$product_id ?>]" type="radio" value="n"<?=($is_markup != 'y') ? ' checked' : "" ?> /><? 
				?><label class="store_components__nav-label store_components__nav-label--minus" for="store_components__nav-input-minus-<?=$product_id ?>">&minus;</label><?
			?></div><?					
			?><input type="text" class="store_components__set_sale" value="<?=($sale_in_product > 0) ? $sale_in_product : "" ?>" name="sale_in_product[<?=$product_id ?>]" placeholder="0" /><?
			?>%<?
		?></div><?
	?></div><? 
	
	if(is_array($db_components) && count($db_components))
	{
		?><p class="store_components__total">
			Итого <strong><?=count($db_components)."</strong> ".format_by_count(count($db_components), "компонент", "компонента", "компонентов") ?> 
			на сумму <strong><?=wc_price($total_price) ?></strong><?
		
			if($sale_price_total > $total_price)
			{
				?> &nbsp;&nbsp;<span style="text-decoration: line-through;"><?=wc_price($sale_price_total) ?></span><?
			}
			
			?>, маржа: <strong><?=wc_price($total_price - $purchase_price) ?></strong><?
			
		?></p><?
	}
	
?></div>
*/


?>