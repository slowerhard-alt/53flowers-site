<?
$xs_data['id'] = (int)$_GET['id'];

if($xs_data['result'] = $wpdb->get_row("SELECT * FROM `xsite_store_components` WHERE `id` = '".$xs_data['id']."'"))
{
	$xs_data['category_id'] = $xs_data['result']->category_id;
	$xs_data['is_favorite'] = $xs_data['result']->favorite;

	if($xs_data['category_id'])
		$xs_data['category_tree'] = get_store_categoriy_parents($xs_data['category_id']);

	if(empty($xs_data['result']->color))
		$xs_data['result']->color = "#ffffff";
	
	// Получаем список всех категорий для форм

	$big_data['category_list'] = get_store_categories(true);
	asort($big_data['category_list']);
	$big_data['category_list'][0] = "- верхний уровень -";

	$xs_data['result']->competitors = json_decode($xs_data['result']->competitors, true);
	$xs_data['result']->ms_components = json_decode($xs_data['result']->ms_components, true);
	$xs_data['result']->sale_rules = json_decode($xs_data['result']->sale_rules, true);

	// Изменяем компонент

	if(isset($_POST['submit_save']))
	{
		$post_data = xs_format($_POST['post_data']);
		
		if(isset($post_data['name']) && !empty($post_data['name']))
		{
			if(isset($post_data['ms_ids']) && !empty($post_data['ms_ids']))
			{
				$set_ms_components = "";
				$post_data['ms_ids'] = sort_ms_ids($post_data['ms_ids']);
			}
			else
				$set_ms_components = ", `ms_components` = ''";
				
			$sale_rules = isset($post_data['sale_rules']) && is_array($post_data['sale_rules'])
				? json_encode($post_data['sale_rules'], JSON_UNESCAPED_UNICODE)
				: "{}";
				
			$wpdb->get_results("
				UPDATE 
					`xsite_store_components`
				SET
					`name` = '".$post_data['name']."',
					`name_for_user` = '".$post_data['name_for_user']."',
					`category_id` = '".(int)$post_data['category_id']."',
					`group_id` = '".(int)$post_data['group_id']."',
					`days` = '".(int)$post_data['days']."',
					`forced_price` = '".(float)$post_data['forced_price']."',
					`purchase_price` = '".(int)$post_data['purchase_price']."',
					`original_price` = '".(int)$post_data['original_price']."',
					`sale_rules` = '".$sale_rules."',
					`favorite` = '".$post_data['favorite']."',
					`show_in_product` = '".$post_data['show_in_product']."',
					`hide_quantity_in_product` = '".$post_data['hide_quantity_in_product']."',
					`is_fresh_delivery` = '".$post_data['is_fresh_delivery']."',
					`is_set_forced_price` = '".$post_data['is_set_forced_price']."',
					`sort` = '".(int)$post_data['sort']."',
					`color` = '".$post_data['color']."',
					`competitors` = '".json_encode($post_data['competitors'], JSON_UNESCAPED_UNICODE)."',
					`ms_ids` = '".$post_data['ms_ids']."',
					`ms_is_update_price` = '".$post_data['ms_is_update_price']."',
					`ms_is_update_days` = '".$post_data['ms_is_update_days']."',
					`ms_quantity_for_days` = '".$post_data['ms_quantity_for_days']."',
					`ms_percent_outstock` = '".$post_data['ms_percent_outstock']."',
					`ms_is_update_retail_price` = '".$post_data['ms_is_update_retail_price']."',
					`ms_image_id` = '".$post_data['ms_image_id']."'
					".$set_ms_components."
				WHERE
					`id` = '".$xs_data['id']."'
			");
			$xs_good[] = "Изменения сохранены";
			
			update_price_component($xs_data['id'], true);
			
			if(!empty($post_data['ms_image_id']) && $post_data['ms_image_id'] != $xs_data['result']->ms_image_id)
				update_image_component_from_ms($post_data['ms_image_id']);
				
			$xs_data['result'] = $wpdb->get_row("SELECT * FROM `xsite_store_components` WHERE `id` = '".$xs_data['id']."'");
			$xs_data['result']->competitors = json_decode($xs_data['result']->competitors, true);
			$xs_data['result']->ms_components = json_decode($xs_data['result']->ms_components, true);
			$xs_data['result']->sale_rules = json_decode($xs_data['result']->sale_rules, true);
			
			$xs_data['category_id'] = $xs_data['result']->category_id;
			$xs_data['is_favorite'] = $xs_data['result']->favorite;
			$xs_data['show_in_product'] = $xs_data['result']->show_in_product;

			if($xs_data['category_id'])
				$xs_data['category_tree'] = get_store_categoriy_parents($xs_data['category_id']);
		}
		else
			$xs_error[] = "Заполнены не все обязательные поля";
	}

	// Изменяем параметры моего склада

	/*
	if(isset($_POST['submit_save_ms']))
	{
		$post_data = xs_format($_POST['post_data']);
		
		if(isset($post_data['ms']) && !empty($post_data['ms']))
		{
			foreach($xs_data['result']->ms_components as $k => $v)
			{
				$xs_data['result']->ms_components[$k]['set_price'] = $post_data['ms'][$v['code']]['set_price'] == 'y'
					? 'y'
					: 'n';
					
				$xs_data['result']->ms_components[$k]['set_days'] = $post_data['ms'][$v['code']]['set_days'] == 'y'
					? 'y'
					: 'n';
			}
			
			$wpdb->get_results("
				UPDATE 
					`xsite_store_components`
				SET
					`ms_components` = '".$wpdb->_real_escape(json_encode($xs_data['result']->ms_components, JSON_UNESCAPED_UNICODE))."'
				WHERE
					`id` = '".$xs_data['id']."'
			");
			$xs_good[] = "Изменения сохранены";
			
			update_price_component($xs_data['id'], true);
		}
		else
			$xs_error[] = "Заполнены не все обязательные поля";
	}
	*/
	
	// Получаем список товаров, в которые входит компонент 53flowers
	
	$xs_data['products_53flowers'] = $wpdb->get_results("
		SELECT
			p.*,
			(SELECT cp.`quantity` FROM `xsite_store_products` cp WHERE cp.`component_id` = '".$xs_data['id']."' AND cp.site = '53flowers' AND cp.product_id = p.`ID`) quantity
		FROM
			`xsite_posts` p
		WHERE
			(
				(
					p.`post_parent` > 0 AND
					p.`post_type` = 'product_variation'
				)
				OR
				(
					p.`post_parent` = 0 AND
					p.`post_type` = 'product' AND 
					(SELECT COUNT(*) FROM `xsite_posts` _p WHERE _p.`post_parent` = p.`ID` AND _p.`post_type` = 'product_variation') = 0
				) 
			) AND
			p.`ID` IN (SELECT cp.`product_id` FROM `xsite_store_products` cp WHERE cp.`component_id` = '".$xs_data['id']."' AND cp.site = '53flowers')
		ORDER BY
			p.`post_parent`, p.`ID`
	");
	
	// Получаем список товаров, в которые входит компонент cvetyru-vn

	$xs_data['products_cvetyru-vn'] = false;
	
	global $wpdbCvetyru;

	$wpdbCvetyru = new wpdb(
		'ct81187vnpro', 
		'r^3O%Wp,CI8l8)', 
		'ct81187_vnpro', 
		'213.171.7.122:3306'
	);
	
	if(empty($wpdbCvetyru->error))
	{
		if($db_products = $wpdb->get_results("SELECT cp.`product_id`, cp.`quantity` FROM `xsite_store_products` cp WHERE cp.`component_id` = '".$xs_data['id']."' AND cp.site = 'cvetyru-vn'"))
		{
			foreach($db_products as $v)
			{
				$ar_products[] = $v->product_id;
				$ar_quantity[$v->product_id] = $v->quantity;
			}
			
			$xs_data['products_cvetyru-vn'] = $wpdbCvetyru->get_results("
				SELECT
					p.*
				FROM
					`wp_posts` p
				WHERE
					(
						(
							p.`post_parent` > 0 AND
							p.`post_type` = 'product_variation'
						)
						OR
						(
							p.`post_parent` = 0 AND
							p.`post_type` = 'product' AND 
							(SELECT COUNT(*) FROM `wp_posts` _p WHERE _p.`post_parent` = p.`ID` AND _p.`post_type` = 'product_variation') = 0
						) 
					) AND
					p.`ID` IN ('".implode("','", $ar_products)."')
				ORDER BY
					p.`post_parent`, p.`ID`
			");
			
			if($xs_data['products_cvetyru-vn'])
				foreach($xs_data['products_cvetyru-vn'] as $k => $v)
					$xs_data['products_cvetyru-vn'][$k]->quantity = $ar_quantity[$v->ID];
		}
	}
	
	$xs_data['result']->ms_ids = sort_ms_ids($xs_data['result']->ms_ids);
}
else
{
	wp_redirect('/wp-admin/admin.php?page='.xs_format($_GET['page']));
	die();
}