<?
$xs_data['category_id'] = (int)$_GET['category_id'];
$xs_data['is_favorite'] = $_GET['favorite'];

$xs_filter = isset($_GET['filter'])
	? xs_format($_GET['filter'])
	: [];

if($xs_data['category_id'])
	$xs_data['category_tree'] = get_store_categoriy_parents($xs_data['category_id']);


// Удаляем категорию

if(isset($_GET['delete']) && (int)$_GET['delete'] > 0)
{
	if((int)$_GET['delete'] != 1)
	{
		$wpdb->get_results("DELETE FROM `xsite_store_categories` WHERE `id` = '".(int)$_GET['delete']."'");
		$wpdb->get_results("UPDATE `xsite_store_categories` SET `parent_id` = '0' WHERE `parent_id` = '".(int)$_GET['delete']."'");
		$wpdb->get_results("UPDATE `xsite_store_components` SET `category_id` = '0' WHERE `category_id` = '".(int)$_GET['delete']."'");
	}
	
	wp_redirect(setUrl($big_data['current_url'], "delete", ""));
	die();
}


// Удаляем компонент

if(isset($_GET['delete_component']) && (int)$_GET['delete_component'] > 0)
{
	if((int)$_GET['delete_component'] != 1)
	{
		if($component = $wpdb->get_row("SELECT * FROM `xsite_store_components` WHERE `id` = '".(int)$_GET['delete_component']."'"))
		{
			unlink($_SERVER['DOCUMENT_ROOT'].$big_data['component_image_path'].$component->image);
			$wpdb->get_results("DELETE FROM `xsite_store_components` WHERE `id` = '".$component->id."'");
			$wpdb->get_results("DELETE FROM `xsite_store_components_to_groups` WHERE `component_id` = '".$component->id."'");
			
			if($products = $wpdb->get_results("SELECT * FROM `xsite_store_products` WHERE `component_id` = '".$component->id."'"))
			{
				foreach($products as $v)
				{
					$wpdb->get_results("DELETE FROM `xsite_store_products` WHERE `id` = '".$v->id."'");
					set_product_price($v->product_id);
				}
			}
			
		}
	}
	
	wp_redirect(setUrl($big_data['current_url'], "delete_component", ""));
	die();
}


// Копируем компонент

if(isset($_GET['copying_component']) && (int)$_GET['copying_component'] > 0)
{
	if((int)$_GET['copying_component'] != 1)
	{
		if($component = $wpdb->get_row("SELECT * FROM `xsite_store_components` WHERE `id` = '".(int)$_GET['copying_component']."'"))
		{
			$wpdb->get_results("
				INSERT 
					INTO `xsite_store_components`
				SET
					`date` = NOW(),
					`name` = '".$wpdb->_real_escape($component->name)." (копия)',
					`name_for_user` = '".$wpdb->_real_escape($component->name_for_user)."',
					`category_id` = '".$component->category_id."',
					`forced_price` = '".$component->forced_price."',
					`purchase_price` = '".$component->purchase_price."',
					`original_price` = '".$component->original_price."',
					`price` = '".$component->price."',
					`sale_price` = '".$component->sale_price."',
					`sale_rules` = '".$component->sale_rules."',
					`show_in_product` = '".$component->show_in_product."',
					`hide_quantity_in_product` = '".$component->hide_quantity_in_product."',
					`is_fresh_delivery` = '".$component->is_fresh_delivery."',
					`is_set_forced_price` = '".$component->is_set_forced_price."',
					`favorite` = '".$component->favorite."',
					`group_id` = '".$component->group_id."',
					`days` = '".$component->days."',
					`sort` = '".$component->sort."',
					`color` = '".$component->color."',
					`competitors` = '".$component->competitors."',
					`ms_ids` = '".$component->ms_ids."',
					`ms_is_update_price` = '".$component->ms_is_update_price."',
					`ms_is_update_days` = '".$component->ms_is_update_days."',
					`ms_quantity_for_days` = '".$component->ms_quantity_for_days."',
					`ms_percent_outstock` = '".$component->ms_percent_outstock."',
					`ms_is_update_retail_price` = '".$component->ms_is_update_retail_price."',
					`ms_image_id` = '".$component->ms_image_id."',
					`image` = '".$component->image."'
			");
			
			if($component_id = $wpdb->insert_id)
				component_create_action($component_id);			
		}
	}
	
	wp_redirect(setUrl($big_data['current_url'], "copying_component", ""));
	die();
}


// Создаём категорию

if(isset($_POST['add_category']))
{
	$post_data = xs_format($_POST['post_data']);
	
	if(isset($post_data['name']) && !empty($post_data['name']))
	{
		$wpdb->get_results("
			INSERT 
				INTO `xsite_store_categories`
			SET
				`name` = '".$post_data['name']."',
				`parent_id` = '".$xs_data['category_id']."',
				`sort` = '".(int)$post_data['sort']."',
				`background` = '".$post_data['background']."',
				`color` = '".$post_data['color']."'
		");
		$xs_good[] = "Категория добавлена";
		
		wp_redirect($big_data['current_url']);
		die();
	}
	else
		$xs_error[] = "Заполнены не все обязательные поля";
}


// Изменяем категорию

if(isset($_POST['edit_category']))
{
	$post_data = xs_format($_POST['post_data']);
	
	if(isset($post_data['name']) && !empty($post_data['name']))
	{
		if((int)$post_data['parent_id'] == (int)$post_data['category_id'])
			$xs_error[] = "Изменения не сохранены, родительская категория не может быть равна текущей";
		else
		{
			$wpdb->get_results("
				UPDATE 
					`xsite_store_categories`
				SET
					`name` = '".$post_data['name']."',
					`parent_id` = '".(int)$post_data['parent_id']."',
					`sort` = '".(int)$post_data['sort']."',
					`background` = '".$post_data['background']."',
					`color` = '".$post_data['color']."'
				WHERE
					`id` = '".(int)$post_data['category_id']."'
			");
			$xs_good[] = "Категория отредактирована";
		
			wp_redirect($big_data['current_url']);
			die();
		}
	}
	else
		$xs_error[] = "Заполнены не все обязательные поля";
}


// Создаём компонент

if(isset($_POST['add_component']))
{
	$post_data = xs_format($_POST['post_data']);
	
	if(isset($post_data['name']) && !empty($post_data['name']))
	{
		$sale_rules = isset($post_data['sale_rules']) && is_array($post_data['sale_rules'])
			? json_encode($post_data['sale_rules'], JSON_UNESCAPED_UNICODE)
			: "{}";
				
		$wpdb->get_results("
			INSERT 
				INTO `xsite_store_components`
			SET
				`name` = '".$post_data['name']."',
				`name_for_user` = '".$post_data['name_for_user']."',
				`category_id` = '".$post_data['category_id']."',
				`forced_price` = '".(float)$post_data['forced_price']."',
				`purchase_price` = '".(int)$post_data['purchase_price']."',
				`original_price` = '".(int)$post_data['original_price']."',
				`sale_rules` = '".$sale_rules."',
				`show_in_product` = '".$post_data['show_in_product']."',
				`hide_quantity_in_product` = '".$post_data['hide_quantity_in_product']."',
				`is_fresh_delivery` = '".$post_data['is_fresh_delivery']."',
				`is_set_forced_price` = '".$post_data['is_set_forced_price']."',
				`favorite` = '".$post_data['favorite']."',
				`group_id` = '".$post_data['group_id']."',
				`days` = '".$post_data['days']."',
				`sort` = '".$post_data['sort']."',
				`color` = '".$post_data['color']."',
				`competitors` = '".json_encode($post_data['competitors'], JSON_UNESCAPED_UNICODE)."',
				`ms_ids` = '".$post_data['ms_ids']."',
				`ms_is_update_price` = '".$post_data['ms_is_update_price']."',
				`ms_is_update_days` = '".$post_data['ms_is_update_days']."',
				`ms_quantity_for_days` = '".$post_data['ms_quantity_for_days']."',
				`ms_percent_outstock` = '".$post_data['ms_percent_outstock']."',
				`ms_is_update_retail_price` = '".$post_data['ms_is_update_retail_price']."',
				`ms_image_id` = '".$post_data['ms_image_id']."'
		");
		
		if($component_id = $wpdb->insert_id)
		{
			component_create_action($component_id);
			update_price_component($component_id);
			
			if(!empty($post_data['ms_image_id']))
				update_image_component_from_ms($post_data['ms_image_id']);
			
			//wp_redirect("/wp-admin/admin.php?page=".xs_format($_GET['page'])."&section=detail&id=".$component_id);
			//die();
		}
	}
	else
		$xs_error[] = "Заполнены не все обязательные поля";
}


$xs_data['categories'] = $wpdb->get_results("SELECT * FROM `xsite_store_categories` WHERE `parent_id` = '".$xs_data['category_id']."' ORDER BY `sort`");


// Получаем список всех категорий для форм

$big_data['category_list'] = get_store_categories(true);
asort($big_data['category_list']);
$big_data['category_list'][0] = "- верхний уровень -";


// Получаем список компонентов

$ar_where = [];

if(isset($xs_filter['search']) && !empty($xs_filter['search']))
{
	$ar_where[] = "`name` LIKE '%".xs_format($xs_filter['search'])."%'";
	$setFilter = true;
}
	
$cat_ids[] = $xs_data['category_id'];

/*
if($xs_data['category_id'] != 0 || $xs_data['is_favorite'] == 'y')
{
	$subcategories = get_store_subcategories_all($xs_data['category_id']);
	foreach($subcategories as $v)
		$cat_ids[] = $v->id;
}
*/

if(!$setFilter)
	$ar_where[] = "`category_id` IN ('".implode("','", $cat_ids)."')";

if($xs_data['is_favorite'] == 'y')
	$ar_where[] = "`favorite` = 'y'";
	
$where = (count($ar_where) > 0) ? " WHERE ".implode(" AND ", $ar_where) : "";

$xs_query = "SELECT * FROM `xsite_store_components`".$where;

$xs_data['components'] = $wpdb->get_results($xs_query.get_order_limit("", ""));
