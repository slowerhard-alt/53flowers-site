<?
global $big_data;

include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

// Фильтры

$setFilter = false;
$ar_where = [];

$xs_filter = isset($_GET['filter'])
	? xs_format($_GET['filter'])
	: [];

$xs_data['product_id'] = isset($_GET['product_id'])
	? (int)xs_format($_GET['product_id'])
	: 0;

$xs_data['order_item_id'] = isset($_GET['order_item_id'])
	? (int)xs_format($_GET['order_item_id'])
	: 0;

if(!$xs_data['product_id'] && !$xs_data['order_item_id'])
	die();

	
if(isset($xs_filter['search']) && !empty($xs_filter['search']))
{
	$xs_filter['search'] = str_replace(" ", "%", xs_format($xs_filter['search']));
	
	$ar_where[] = "(`name` LIKE '%".$xs_filter['search']."%' OR `name_for_user` LIKE '%".xs_format($xs_filter['search'])."%')";
	$setFilter = true;
}

if(isset($xs_filter['category_id']) && !empty($xs_filter['category_id']) && $xs_filter['category_id'] != 0)
{
	$cat_ids[] = $xs_filter['category_id'];

	$subcategories = get_store_subcategories_all($xs_filter['category_id']);
	foreach($subcategories as $v)
		$cat_ids[] = $v->id;
	
	$ar_where[] = "`category_id` IN ('".implode("','", $cat_ids)."')";
	$setFilter = true;
}

if(isset($xs_filter['favorite']) && $xs_filter['favorite'] == 'y')
{
	$ar_where[] = "`favorite` = 'y'";
	$setFilter = true;
}

$where = (count($ar_where) > 0) ? " WHERE ".implode(" AND ", $ar_where) : "";


$xs_query = "SELECT * FROM `xsite_store_components`".$where;

$big_data['number'] = 8;
$big_data['offset'] = ($big_data['paged'] - 1) * $big_data['number'];

$xs_data['components'] = $wpdb->get_results($xs_query.get_order_limit('sort', 'asc'));
$xs_total = $wpdb->get_var(preg_replace('|(SELECT).+(FROM)|isU', "$1"." COUNT(*) "."$2",$xs_query));


// Получаем список всех категорий для форм

$big_data['category_list'] = get_store_categories();

if($xs_data['product_id'])
{
	$xs_data['product_components'] = get_store_components_product($xs_data['product_id']);
	$product_id = $xs_data['product_id'];
}
elseif($xs_data['order_item_id'])
	$xs_data['product_components'] = get_store_components_order($xs_data['order_item_id']);

?><div class="select_component_container">
	<div class="select_component_container__loader">
		<form class="xs_filter" method="get" action="">
			<label class="long">
				<input type="text" name="filter[search]" value="<?=$xs_filter['search']?>" placeholder="Поиск" />
			</label>
			<label>
				<input onchange="jQuery(this).parents('.xs_filter').submit()" type="checkbox" name="filter[favorite]"<?=$xs_filter['favorite'] == 'y' ? " checked" : "" ?> value="y" /> Избранное
			</label>
			
			<input type="hidden" name="filter[category_id]" value="<?=(int)xs_format($xs_filter['category_id']) ?>" />
			<input type="hidden" name="product_id" value="<?=isset($_GET['product_id']) ? (int)xs_format($_GET['product_id']) : 0 ?>" />
			<input type="hidden" name="order_item_id" value="<?=isset($_GET['order_item_id']) ? (int)xs_format($_GET['order_item_id']) : 0 ?>" />
			
			<label class="xs_submit">
				<input type="submit" value="Искать" class="button-primary" />
			</label><?
			
			if($setFilter)
			{
				?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="
					jQuery(this).parents('.xs_filter').find('input[name=\'filter[category_id]\']').val('')
					jQuery(this).parents('.xs_filter').find('input[type=text]').val('')
					jQuery(this).parents('.xs_filter').find('select').val('')
					jQuery(this).parents('.xs_filter').find('input[type=checkbox]').prop('checked', false)
					jQuery(this).parents('.xs_filter').submit()
					return false
				">× очистить фильтр</a><?
			}
			
			?><input type="hidden" value="1" name="paged" />

		</form><?

		?><div class="filter_report_cat_container"><?

			$_ar_parents = false;
			$ar_parents = [];
			
			if(isset($xs_filter['category_id']) && !empty($xs_filter['category_id']))
			{
				if($_ar_parents = get_store_categoriy_parents($xs_filter['category_id']))
					foreach($_ar_parents as $v)
						$ar_parents[] = $v->parent_id;
				
				$ar_parents[] = $xs_filter['category_id'];
			}
			else
				$ar_parents[] = 0;
				
			foreach($ar_parents as $parent_id)
			{
				$ar_childrens = [];
				
				foreach($big_data['category_list'] as $v)
					if($v->parent_id == $parent_id)
						$ar_childrens[] = $v;
				
				if(count($ar_childrens))
				{
					?><div class="filter_report_cat"><?
					
						if($parent_id == 0)
						{
							?><div class="filter_report_cat__item<?=!isset($xs_filter['category_id']) || empty($xs_filter['category_id']) || $xs_filter['category_id'] == 0 ? " filter_report_cat__item--selected" : "" ?>" onclick="jQuery('.xs_filter input[name=\'filter[category_id]\']').val('0'); jQuery('.xs_filter').submit()">Все</div><?
						}
						
						foreach($ar_childrens as $v)
						{
							?><div class="filter_report_cat__item<?=($_ar_parents && in_array($v->id, $ar_parents)) ? " filter_report_cat__item--selected" : "" ?>" onclick="jQuery('.xs_filter input[name=\'filter[category_id]\']').val('<?=$v->id ?>'); jQuery('.xs_filter').submit()"><?=$v->name ?></div><?
						}
						
					?></div><?
				}
			}

		?></div><?

		?><div class="content_container"><?
		
			?><div id="xs_current_page" style="display:none"><?=$big_data['paged'] ?></div><?
			?><form><?

				if(is_array($xs_data['components']) && count($xs_data['components']))
				{
					?><div class="component_table">
						<table class="component_table__table wp-list-table widefat striped table-view-list posts">
							<tbody><? 
							
							foreach($xs_data['components'] as $v)
							{
								$v->component_id = $v->id;
								
								?><tr class="component_table__item item<?=isset($xs_data['product_components'][$v->id]) && $xs_data['product_components'][$v->id] > 0 ? " active" : "" ?>" data-component_id="<?=$v->component_id ?>">
									<td class="component_table__td-image">
										<span class="component_table__image"<?=!empty($v->image) ? ' style="background-image:url('.$big_data['component_image_path'].$v->image.')"' : "" ?>></span>
									</td>
									<td class="component_table__td-name">
										<div class="component_table__name">
											<a href="/wp-admin/admin.php?page=store&section=detail&id=<?=$v->component_id ?>" target="_blank"><?=$v->name ?></a>
										</div>
									</td>
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
											?><input type="text" value="<?=isset($xs_data['product_components'][$v->id]) ? $xs_data['product_components'][$v->id] : 0 ?>" /><?
											?><span class="plus"></span><?
										?></div>
									</td>
									<td class="component_table__td-view">
										<span title="Отображать для покупателей в карточке товара" class="component_table__set component_table__set--view xs_set_view<?=$v->show_in_product == 'y' ? " active" : "" ?>" data-id="<?=$v->component_id ?>"><? get_svg("view", $v->color) ?></span>
									</td>
									<td class="component_table__td-favorite">
										<span title="Избранное" class="component_table__set component_table__set--favorite xs_set_favorite<?=$v->favorite == 'y' ? " active" : "" ?>" data-id="<?=$v->component_id ?>"><? get_svg("star", $v->color) ?></span>
									</td>		
									<td class="component_table__td-price"><?
											
										if($v->sale_price > 0)
										{
											?><span class="component_table__sale_price"><?=wc_price($v->sale_price, ['decimals' => 2]) ?></span><?
										}
										
										?><span class="component_table__price<?=$v->sale_price > 0 ? ' xs_red' : ""?>"><?=wc_price($v->price, ['decimals' => 2]) ?></span>
									</td>		
									<td class="component_table__td-total"><?
									
										if(isset($xs_data['product_components'][$v->id]) && $xs_data['product_components'][$v->id] > 0)
										{
											?><span class="component_table__price<?=$v->price <= 0 ? ' xs_red' : ""?>"><?=wc_price($v->price * $xs_data['product_components'][$v->id], ['decimals' => 2]) ?></span><?
										}
										else
										{
											?><span class="group_days days_<?=$v->days ?> group_<?=$v->group_id ?>"><?
												?><span class="group"><?=get_store_group_name($v->group_id) ?></span>&nbsp;/&nbsp;<? 
												?><span class="days"><?=$v->days ?>&nbsp;дн</span><?
											?></span><?
										}
										
									?></td>		
								</tr><?  
							}
							
							?></tbody>
						</table>
					</div><?





					/*
					?><div class="store_components xs_flex xs_wrap xs_start"><?
					
						foreach($xs_data['components'] as $v)
						{
							?><div class="item<?=isset($xs_data['product_components'][$v->id]) && $xs_data['product_components'][$v->id] > 0 ? " active" : "" ?>"><?
								
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
					
								?><div class="xs_quantity xs_flex" <?
								
									if($xs_data['product_id'])
									{
										?>data-product_id="<?=$xs_data['product_id'] ?>"<?
									}
									else
									{
										?>data-order_item_id="<?=$xs_data['order_item_id'] ?>"<?
									}
									
									?>data-component_id="<?=$v->id ?>"><?
									?><span class="minus"></span><?
									?><input type="text" value="<?=isset($xs_data['product_components'][$v->id]) ? $xs_data['product_components'][$v->id] : 0 ?>" /><?
									?><span class="plus"></span><?
								?></div><?
								
								?><span class="xs_set_favorite favorite xs_flex xs_middle xs_center<?=$v->favorite == 'y' ? " active" : "" ?>" data-id="<?=$v->id ?>"><? get_svg("star", $v->color) ?></span><?
								
								?><span class="xs_set_view view xs_flex xs_middle xs_center<?=$v->show_in_product == 'y' ? " active" : "" ?>" data-id="<?=$v->id ?>"><? get_svg("view", $v->color) ?></span><?
								
								?><div class="link"><?
									?><span class="image"<?=!empty($v->image) ? ' style="background-image:url('.$big_data['component_image_path'].$v->image.')"' : "" ?>></span><?
									?><a href="/wp-admin/admin.php?page=store&section=detail&id=<?=$v->id ?>" class="name_container" target="_blank"><?=$v->name ?></a><?
									?><span class="xs_flex pq_container"><?
										?><span class="price<?=$v->price <= 0 ? ' xs_red' : ""?>"><?=wc_price($v->price, ['decimals' => 2]) ?></span><?
							
										if($v->sale_price > 0)
										{
											?><span class="sale_price"><?=wc_price($v->sale_price, ['decimals' => 2]) ?></span><?
										}
										
										if(isset($xs_data['product_components'][$v->id]) && $xs_data['product_components'][$v->id] > 0)
										{
											?><span class="price<?=$v->price <= 0 ? ' xs_red' : ""?>"><?=wc_price($v->price * $xs_data['product_components'][$v->id], ['decimals' => 2]) ?></span><?
										}
										else
										{
											?><span class="group_days days_<?=$v->days ?> group_<?=$v->group_id ?>"><?
												?><span class="group"><?=get_store_group_name($v->group_id) ?></span>&nbsp;/&nbsp;<? 
												?><span class="days"><?=$v->days ?>&nbsp;дн</span><?
											?></span><?
										}
										
									?></span><?
								?></div><?
							?></div><?
						}
					
					?></div><?
					*/
				}
				
				?><div class="clear"></div><?

			?></form><?
			
			?><div class="xs_pages xs_flex xs_middle"><?		
				?><form method="post" action=""><?
					
					echo paginate_links(array(  
						  'base'      => str_replace(['&paged=0', '?paged=0'], '', setUrl($big_data['current_url'], 'paged', '0')).'%_%',  
						  'format'    => '&paged=%#%',  
						  'current'   => $big_data['paged'],  
						  'total'     => ceil($xs_total / $big_data['number']),  
						  'prev_next' => false,  
						  'type'      => 'list',  
					)); 

				?></form><? 
				
				if($xs_data['product_id'])
				{
					$c = is_array($xs_data['product_components'])
						? count($xs_data['product_components'])
						: 0;

					?><div class="xs_total">Выбрано компонентов: <strong class="total_quantity"><?=$c ?></strong> на сумму <strong class="total_price"><?=wc_price(($c == 0 ? 0 : get_post_meta($xs_data['product_id'], '_regular_price', true)), ['decimals' => 2]) ?></strong></div><?
				}
				
				?><div class="xs_flex xs_middle"><?
					?><input type="submit" class="button-primary" onclick="jQuery.fancybox.close(); return false" value="Применить и закрыть" /><?
				?></div><?
			?></div><?
		?></div><?
	?></div><?
?></div><?