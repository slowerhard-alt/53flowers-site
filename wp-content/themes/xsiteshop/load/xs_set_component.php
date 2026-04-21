<?
global $big_data;
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

if(!is_user_logged_in())
	die();

$result = [];

$post_data = xs_format($_POST);

$component_id = (int)$post_data['component_id'];
$purchase_price = (int)$post_data['purchase_price'];
$original_price = (int)$post_data['original_price'];
$forced_price = (float)round($post_data['forced_price'], 2);
$days = (int)$post_data['days'];
$group_id = (int)$post_data['group_id'];
$sale_rules = $post_data['sale_rules'];
$sort = (int)$post_data['sort'];
$name = $post_data['name'];
$name_for_user = $post_data['name_for_user'];
$category_id = $post_data['category_id'];
$is_set_forced_price = $post_data['is_set_forced_price'] == 'y' ? 'y' : '';
$color = $post_data['color'];
$show_in_product = $post_data['show_in_product'] == 'y' ? 'y' : '';
$hide_quantity_in_product = $post_data['hide_quantity_in_product'] == 'y' ? 'y' : '';
$is_fresh_delivery = $post_data['is_fresh_delivery'] == 'y' ? 'y' : '';
$favorite = $post_data['favorite'] == 'y' ? 'y' : '';
$ms_ids = sort_ms_ids($post_data['ms_ids']);
$ms_is_update_price = $post_data['ms_is_update_price'] == 'y' ? 'y' : '';
$ms_is_update_days = $post_data['ms_is_update_days'] == 'y' ? 'y' : '';
$ms_quantity_for_days =(int)$post_data['ms_quantity_for_days'];
$ms_percent_outstock = (float)$post_data['ms_percent_outstock'];
$ms_is_update_retail_price = $post_data['ms_is_update_retail_price'] == 'y' ? 'y' : '';
$ms_image_id =$post_data['ms_image_id'];
$competitors = $post_data['competitors'];
$is_group = $post_data['is_group'] == 'y';
$ar_competitors = [];

if($component_id)
{
	$ar_components = [];
	$count_update_image = [];
	
	if($is_group)
	{
		$group = get_group_components($component_id);
		
		if($group && isset($group->components))
			$ar_components = $group->components;
	}
	elseif($component = $wpdb->get_row("SELECT * FROM `xsite_store_components` WHERE `id` = '".$component_id."'"))
		$ar_components[] = $component;
	
	if(count($ar_components) > 0)
	{
		foreach($ar_components as $component)
		{
			$db_competitors = json_decode($component->competitors, true);
			$db_sale_rules = json_decode($component->sale_rules, true);
			
			for($i = 0; $i < $big_data['store_competitors_count']; $i++)
			{
				$ar_competitors[] = [
					'p' => isset($competitors[$i]) ? (int)$competitors[$i] : (int)$db_competitors[$i]['p'],
					'l' => isset($db_competitors[$i]) ? $db_competitors[$i]['l'] : ""
				];
			}
			
			$ar_set = [];
				
			if(is_row('group'))
				$ar_set[] = "`group_id` = '".$group_id."'";
			
			if(is_row('days'))
				$ar_set[] = "`days` = '".$days."'";
			
			if(is_row('forced_price'))
				$ar_set[] = "`forced_price` = '".$forced_price."'";
			
			if(is_row('purchase_price'))
				$ar_set[] = "`purchase_price` = '".$purchase_price."'";
			
			if(is_row('original_price'))
				$ar_set[] = "`original_price` = '".$original_price."'";
			
			if(is_row('sale_type') || is_row('sale_percent') || is_row('sale_min_quantity') || is_row('sale_min_days'))  
			{
				if(is_row('sale_type'))
					$db_sale_rules['type'] = $sale_rules['type'];
				
				if(is_row('sale_percent'))
					$db_sale_rules['percent'] = $sale_rules['percent'];
				
				if(is_row('sale_min_quantity')) 
					$db_sale_rules['min_quantity'] = $sale_rules['min_quantity'];
				
				if(is_row('sale_min_days'))
					$db_sale_rules['min_days'] = $sale_rules['min_days'];
				
				$ar_set[] = "`sale_rules` = '".json_encode($db_sale_rules, JSON_UNESCAPED_UNICODE)."'";
			}
			
			if(!$is_group && is_row('sort'))
				$ar_set[] = "`sort` = '".$sort."'";
			
			if(!$is_group && is_row('name'))
				$ar_set[] = "`name` = '".$name."'";
			
			if(is_row('name_for_user'))
				$ar_set[] = "`name_for_user` = '".$name_for_user."'";
			
			if(is_row('category_id'))
				$ar_set[] = "`category_id` = '".$category_id."'";
			
			if(is_row('is_set_forced_price'))
				$ar_set[] = "`is_set_forced_price` = '".$is_set_forced_price."'";
			
			if(is_row('color'))
				$ar_set[] = "`color` = '".$color."'";
			
			if(is_row('show_in_product'))
				$ar_set[] = "`show_in_product` = '".$show_in_product."'";
			
			if(is_row('hide_quantity_in_product'))
				$ar_set[] = "`hide_quantity_in_product` = '".$hide_quantity_in_product."'";
			
			if(is_row('is_fresh_delivery'))
				$ar_set[] = "`is_fresh_delivery` = '".$is_fresh_delivery."'";
			
			if(is_row('favorite'))
				$ar_set[] = "`favorite` = '".$favorite."'";
			
			if(is_row('ms_ids'))
			{
				$ar_set[] = "`ms_ids` = '".$ms_ids."'";
				
				if(empty($ms_ids))
					$ar_set[] = "`ms_components` = ''";
			}
			
			if(is_row('ms_is_update_price'))
				$ar_set[] = "`ms_is_update_price` = '".$ms_is_update_price."'";
			
			if(is_row('ms_is_update_days'))
				$ar_set[] = "`ms_is_update_days` = '".$ms_is_update_days."'";
			
			if(is_row('ms_quantity_for_days'))
				$ar_set[] = "`ms_quantity_for_days` = '".$ms_quantity_for_days."'";
			
			if(is_row('ms_percent_outstock'))
				$ar_set[] = "`ms_percent_outstock` = '".$ms_percent_outstock."'";
			
			if(is_row('ms_is_update_retail_price'))
				$ar_set[] = "`ms_is_update_retail_price` = '".$ms_is_update_retail_price."'";
			
			if(is_row('ms_image_id'))
			{
				$ar_set[] = "`ms_image_id` = '".$ms_image_id."'";
				
				if($component->ms_image_id != $ms_image_id)
					$count_update_image[] = $ms_image_id;
			}
			
			if(is_row('competitors'))
				$ar_set[] = "`competitors` = '".json_encode($ar_competitors, JSON_UNESCAPED_UNICODE)."'";
			
			if(count($ar_set))
			{
				$wpdb->get_results("
					UPDATE 
						`xsite_store_components`
					SET
						".implode(", ", $ar_set)."
					WHERE
						`id` = '".$component->id."'
				");
			}
			
			update_price_component($component->id);
		}
	}
	
	if(count($count_update_image) > 0)
		update_image_component_from_ms($count_update_image);
	
	if($is_group)
	{
		$v = get_group_components($component_id);
		$v->is_group = 'y';
	}
	else
	{
		$xs_query = "
			SELECT 
				c.*,
				cc.`name` `category_name`
			FROM `xsite_store_components` AS c
			LEFT JOIN `xsite_store_categories` cc ON cc.`id` = c.`category_id`
			WHERE c.`id` = '".$component_id."'";

		$v = $wpdb->get_row($xs_query);
		$v->sale_rules = json_decode($v->sale_rules, true);
	}
	
	$big_data['category_list_all'] = get_store_categories(true);
	
	include "../include/admin/store/templates/row.php";
}