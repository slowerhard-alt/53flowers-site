<?
$xs_filter = isset($_GET['filter']) ? xs_format($_GET['filter']) : array();

$setFilter = false;
$where = [];

$xs_data = (object)[
	'action' => xs_format($_GET['action']) == 'edit' ? 'edit' : 'view'
];

$all_columns = [
	'sort'                    => 'Сорт',
	'category_id'             => 'Категория',
	'name'                    => 'Название',
	'name_for_user'           => 'Название для покупателей',
	'sale_type'               => 'Тип скидки',
	'group'                   => 'Группа',
	'coefficient'             => 'Коэффициент',
	'days'                    => 'Дни',
	'ms_quantity'             => 'Остаток',
	'purchase_price'          => 'Закуп цена',
	'original_price'          => 'Себестоимость',
	'forced_price'            => 'Приоритетная цена',
	'sale_percent'            => 'Процент скидки',
	'price'                   => 'Цена',
	'sale_price'              => 'Цена без скидки',
	'sale_forced_price'       => 'Приор цена без скидки',
	'is_set_forced_price'     => 'Дни влияют на приор цену',
	'sale_min_quantity'       => 'Мин. остаток для скидки',
	'sale_min_days'           => 'Мин. дней для скидки',
	'color'                   => 'Цвет',
	'show_in_product'         => 'Показывать в товаре',
	'hide_quantity_in_product'=> 'Скрыть количество в товаре',
	'is_fresh_delivery'       => 'Свежая поставка',
	'favorite'                => 'Избранное',
	'ms_ids'                  => 'ID МойСклад',
	'ms_is_update_price'      => 'Обновлять себест с МС',
	'ms_is_update_days'       => 'Обновлять дни с МС',
	'ms_quantity_for_days'    => 'Остаток для дней МС',
	'ms_percent_outstock'     => '% при нулевом остатке',
	'ms_is_update_retail_price'=> 'Обновлять розницу в МС',
	'ms_image_id'             => 'ID фото МС',
];

$default_presets = [
	'preset1' => ['label' => 'Цены',      'rows' => ['name','days','ms_quantity','purchase_price','original_price','price']],
	'preset2' => ['label' => 'МойСклад',  'rows' => ['name','ms_ids','ms_quantity','ms_is_update_price','ms_is_update_days']],
	'preset3' => ['label' => 'Скидки',    'rows' => ['name','sale_type','sale_percent','sale_min_quantity','sale_min_days']],
	'preset4' => ['label' => 'Полный',    'rows' => array_keys($all_columns)],
];

$user_presets = get_user_meta($big_data['current_user']->ID, 'xs_preset_definitions', true);
if(!is_array($user_presets) || empty($user_presets)) $user_presets = $default_presets;

$big_data['user_presets'] = $user_presets;
$big_data['all_columns']  = $all_columns;

if(isset($_POST['save_preset_config']) && isset($_POST['preset_label']) && is_array($_POST['preset_label']))
{
	$new_presets = [];
	foreach($_POST['preset_label'] as $key => $label)
	{
		$key = xs_format($key);
		$label = trim(xs_format($label));
		if(empty($label)) $label = 'Пресет';
		$rows = (isset($_POST['preset_rows'][$key]) && is_array($_POST['preset_rows'][$key]))
			? array_keys($_POST['preset_rows'][$key])
			: [];
		$new_presets[$key] = ['label' => $label, 'rows' => $rows];
	}
	update_user_meta($big_data['current_user']->ID, 'xs_preset_definitions', $new_presets);
	$user_presets = $new_presets;
	wp_redirect($big_data['current_url']);
	exit;
}

if(isset($_GET['xs_preset']) && isset($user_presets[$_GET['xs_preset']]))
{
	$preset_rows = $user_presets[$_GET['xs_preset']]['rows'];
	$ar_rows = [];
	foreach($preset_rows as $col)
		$ar_rows[xs_format($col)] = 'y';
	update_user_meta($big_data['current_user']->ID, 'xs_rows', $ar_rows);
	$redirect_url = remove_query_arg('xs_preset', $big_data['current_url']);
	wp_redirect($redirect_url);
	exit;
}

if(isset($_POST['set_rows']) && is_array($_POST['row']))
{
	$ar_rows = xs_format($_POST['row']);
	update_user_meta($big_data['current_user']->ID, 'xs_rows', $ar_rows);

	wp_redirect($big_data['current_url']);
	exit;
}

if(isset($_POST['save_one']) && $_POST['save_one'] == 'y' && isset($_POST['c']) && is_array($_POST['c']))
{
	$post_components = xs_format($_POST['c']);
	$component_id = key($post_components);
	$component = $post_components[$component_id];

	if($db_component = $wpdb->get_row("SELECT * FROM `xsite_store_components` WHERE `id` = '".$component_id."'"))
	{
		$db_competitors = json_decode($db_component->competitors, true);
		$db_sale_rules = json_decode($db_component->sale_rules, true);

		$ar_competitors = [];
		for($i = 0; $i < $big_data['store_competitors_count']; $i++)
		{
			$ar_competitors[] = [
				'p' => isset($component['competitors'][$i]) ? (int)$component['competitors'][$i] : (int)$db_competitors[$i]['p'],
				'l' => isset($db_competitors[$i]) ? $db_competitors[$i]['l'] : ""
			];
		}

		$ar_set = [];

		if(isset($component['group_id']))
			$ar_set[] = "`group_id` = '".$component['group_id']."'";
		if(isset($component['days']))
			$ar_set[] = "`days` = '".(int)$component['days']."'";
		if(isset($component['forced_price']))
			$ar_set[] = "`forced_price` = '".(float)$component['forced_price']."'";
		if(isset($component['purchase_price']))
			$ar_set[] = "`purchase_price` = '".(float)$component['purchase_price']."'";
		if(isset($component['original_price']))
			$ar_set[] = "`original_price` = '".(float)$component['original_price']."'";
		if(isset($component['sale_rules']))
		{
			if(isset($component['sale_rules']['type']))
				$db_sale_rules['type'] = $component['sale_rules']['type'];
			if(isset($component['sale_rules']['percent']))
				$db_sale_rules['percent'] = $component['sale_rules']['percent'];
			if(isset($component['sale_rules']['min_quantity']))
				$db_sale_rules['min_quantity'] = $component['sale_rules']['min_quantity'];
			if(isset($component['sale_rules']['min_days']))
				$db_sale_rules['min_days'] = $component['sale_rules']['min_days'];
			$ar_set[] = "`sale_rules` = '".json_encode($db_sale_rules, JSON_UNESCAPED_UNICODE)."'";
		}
		if(isset($component['sort']))
			$ar_set[] = "`sort` = '".(int)$component['sort']."'";
		if(isset($component['name']))
			$ar_set[] = "`name` = '".$component['name']."'";
		if(isset($component['name_for_user']))
			$ar_set[] = "`name_for_user` = '".$component['name_for_user']."'";
		if(isset($component['category_id']))
			$ar_set[] = "`category_id` = '".(int)$component['category_id']."'";
		if(isset($component['is_set_forced_price']))
			$ar_set[] = "`is_set_forced_price` = '".($component['is_set_forced_price'] == 'y' ? 'y' : '')."'";
		if(isset($component['color']))
			$ar_set[] = "`color` = '".$component['color']."'";
		if(isset($component['show_in_product']))
			$ar_set[] = "`show_in_product` = '".($component['show_in_product'] == 'y' ? 'y' : '')."'";
		if(isset($component['hide_quantity_in_product']))
			$ar_set[] = "`hide_quantity_in_product` = '".($component['hide_quantity_in_product'] == 'y' ? 'y' : '')."'";
		if(isset($component['is_fresh_delivery']))
			$ar_set[] = "`is_fresh_delivery` = '".($component['is_fresh_delivery'] == 'y' ? 'y' : '')."'";
		if(isset($component['favorite']))
			$ar_set[] = "`favorite` = '".($component['favorite'] == 'y' ? 'y' : '')."'";
		if(isset($component['ms_ids']))
		{
			$component['ms_ids'] = sort_ms_ids($component['ms_ids']);
			$ar_set[] = "`ms_ids` = '".$component['ms_ids']."'";
		}
		if(isset($component['ms_is_update_price']))
			$ar_set[] = "`ms_is_update_price` = '".($component['ms_is_update_price'] == 'y' ? 'y' : '')."'";
		if(isset($component['ms_is_update_days']))
			$ar_set[] = "`ms_is_update_days` = '".($component['ms_is_update_days'] == 'y' ? 'y' : '')."'";
		if(isset($component['ms_quantity_for_days']))
			$ar_set[] = "`ms_quantity_for_days` = '".(int)$component['ms_quantity_for_days']."'";
		if(isset($component['ms_percent_outstock']))
			$ar_set[] = "`ms_percent_outstock` = '".(float)$component['ms_percent_outstock']."'";
		if(isset($component['ms_is_update_retail_price']))
			$ar_set[] = "`ms_is_update_retail_price` = '".($component['ms_is_update_retail_price'] == 'y' ? 'y' : '')."'";
		if(isset($component['ms_image_id']))
			$ar_set[] = "`ms_image_id` = '".$component['ms_image_id']."'";

		if(count($ar_set))
		{
			$wpdb->get_results("
				UPDATE
					`xsite_store_components`
				SET
					".implode(", ", $ar_set)."
				WHERE
					`id` = '".$component_id."'
			");

			update_price_component($component_id, false, false, false);
		}

		$_log_fields = array_keys($component);
		if(function_exists("xs_log"))
			xs_log("SAVE_ONE", "component_id:" . $component_id . " fields:[" . implode(",", $_log_fields) . "] user:" . get_current_user_id() . " result:OK");

		header('Content-Type: application/json');
		echo json_encode(['success' => true]);
		exit;
	}

	if(function_exists("xs_log"))
		xs_log("SAVE_ONE", "component_id:" . $component_id . " ERROR:Component not found");
	header('Content-Type: application/json');
	echo json_encode(['success' => false, 'error' => 'Component not found']);
	exit;
}

if(isset($_POST['edit_components']) && $_POST['edit_components'] == 'y' && $xs_data->action == 'edit')
{
	$count_update_image = [];
	$count_result = 0;
	$count_error = 0;
	$ar_notify = [];
	$post_components = xs_format($_POST['c']);

	foreach($post_components as $component_id => $component)
	{
		if($db_component = $wpdb->get_row("SELECT * FROM `xsite_store_components` WHERE `id` = '".$component_id."'"))
		{
			$db_competitors = json_decode($db_component->competitors, true);
			$db_sale_rules = json_decode($db_component->sale_rules, true);

			$ar_competitors = [];

			for($i = 0; $i < $big_data['store_competitors_count']; $i++)
			{
				$ar_competitors[] = [
					'p' => isset($component['competitors'][$i]) ? (int)$component['competitors'][$i] : (int)$db_competitors[$i]['p'],
					'l' => isset($db_competitors[$i]) ? $db_competitors[$i]['l'] : ""
				];
			}

			$ar_set = [];

			if(is_row('group'))
				$ar_set[] = "`group_id` = '".$component['group_id']."'";

			if(is_row('days'))
				$ar_set[] = "`days` = '".$component['days']."'";

			if(is_row('forced_price'))
				$ar_set[] = "`forced_price` = '".$component['forced_price']."'";

			if(is_row('purchase_price'))
				$ar_set[] = "`purchase_price` = '".$component['purchase_price']."'";

			if(is_row('original_price'))
				$ar_set[] = "`original_price` = '".$component['original_price']."'"; 

			if(is_row('sale_type') || is_row('sale_percent') || is_row('sale_min_quantity') || is_row('sale_min_days'))  
			{
				if(is_row('sale_type'))
					$db_sale_rules['type'] = $component['sale_rules']['type'];

				if(is_row('sale_percent'))
					$db_sale_rules['percent'] = $component['sale_rules']['percent'];

				if(is_row('sale_min_quantity')) 
					$db_sale_rules['min_quantity'] = $component['sale_rules']['min_quantity'];

				if(is_row('sale_min_days'))
					$db_sale_rules['min_days'] = $component['sale_rules']['min_days'];

				$ar_set[] = "`sale_rules` = '".json_encode($db_sale_rules, JSON_UNESCAPED_UNICODE)."'";
			}

			if(is_row('sort'))
				$ar_set[] = "`sort` = '".$component['sort']."'";

			if(is_row('name'))
				$ar_set[] = "`name` = '".$component['name']."'";

			if(is_row('name_for_user'))
				$ar_set[] = "`name_for_user` = '".$component['name_for_user']."'";

			if(is_row('category_id'))
				$ar_set[] = "`category_id` = '".(int)$component['category_id']."'";

			if(is_row('is_set_forced_price'))
				$ar_set[] = "`is_set_forced_price` = '".($component['is_set_forced_price'] == 'y' ? 'y' : '')."'";

			if(is_row('color'))
				$ar_set[] = "`color` = '".$component['color']."'";

			if(is_row('show_in_product'))
				$ar_set[] = "`show_in_product` = '".($component['show_in_product'] == 'y' ? 'y' : '')."'";

			if(is_row('hide_quantity_in_product'))
				$ar_set[] = "`hide_quantity_in_product` = '".($component['hide_quantity_in_product'] == 'y' ? 'y' : '')."'";

			if(is_row('is_fresh_delivery'))
				$ar_set[] = "`is_fresh_delivery` = '".($component['is_fresh_delivery'] == 'y' ? 'y' : '')."'";

			if(is_row('favorite'))
				$ar_set[] = "`favorite` = '".($component['favorite'] == 'y' ? 'y' : '')."'";

			if(is_row('ms_ids'))
			{
				$component['ms_ids'] = sort_ms_ids($component['ms_ids']);
				$ar_set[] = "`ms_ids` = '".$component['ms_ids']."'";

				if(empty($ms_ids))
					$ar_set[] = "`ms_components` = ''";
			}

			if(is_row('ms_is_update_price'))
				$ar_set[] = "`ms_is_update_price` = '".($component['ms_is_update_price'] == 'y' ? 'y' : '')."'";

			if(is_row('ms_is_update_days'))
				$ar_set[] = "`ms_is_update_days` = '".($component['ms_is_update_days'] == 'y' ? 'y' : '')."'";

			if(is_row('ms_quantity_for_days'))
				$ar_set[] = "`ms_quantity_for_days` = '".(int)$component['ms_quantity_for_days']."'";

			if(is_row('ms_percent_outstock'))
				$ar_set[] = "`ms_percent_outstock` = '".(float)$component['ms_percent_outstock']."'";

			if(is_row('ms_is_update_retail_price'))
				$ar_set[] = "`ms_is_update_retail_price` = '".($component['ms_is_update_retail_price'] == 'y' ? 'y' : '')."'";

			if(is_row('ms_image_id'))
			{
				$ar_set[] = "`ms_image_id` = '".$component['ms_image_id']."'";

				if($db_component->ms_image_id != $component['ms_image_id'])
					$count_update_image[] = $component['ms_image_id'];
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
						`id` = '".$component_id."'
				");
			}

			$notify_parts = array();
			if(is_row('forced_price') && $component['forced_price'] !== '' && (float)$component['forced_price'] != (float)$db_component->forced_price)
				$notify_parts[] = (float)$component['forced_price'] . '₽';
			if(is_row('sale_percent') && isset($component['sale_rules']['percent']) && $component['sale_rules']['percent'] !== '' && (int)$component['sale_rules']['percent'] > 0)
				$notify_parts[] = '−' . (int)$component['sale_rules']['percent'] . '%';
			if(count($notify_parts))
				$ar_notify[] = $db_component->name . ': ' . implode(' ', $notify_parts);

			$count_result++;
		}
		else
			$count_error++;
	}

	if(count($count_update_image) > 0)
		update_image_component_from_ms($count_update_image);

	$xs_good[] = "Все цены успешно обновлены.";
	if(function_exists("xs_log"))
		xs_log("EDIT_BULK", "count:" . $count_result . " user:" . get_current_user_id() . " errors:" . $count_error . " result:OK");

	$is_send_telegram = isset($_POST['is_send_telegram']) && $_POST['is_send_telegram'] == 'y';
	$is_send_bitrix = isset($_POST['is_send_bitrix']) && $_POST['is_send_bitrix'] == 'y';

	update_price_component(0, false, $is_send_telegram, $is_send_bitrix);

	if($is_send_bitrix && count($ar_notify))
	{
		if(!class_exists('bitrix24'))
			include $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/xsiteshop/include/class/bitrix24.php';

		$b24 = new bitrix24;
		$msg = '[B]Я поменял цены:[/B]' . "\n" . implode("\n", array_map(function($s) { return '• ' . $s; }, $ar_notify));
		$b24->send_price_message('chat44819', $msg);
	}

	$is_create_task = isset($_POST['is_create_task']) && $_POST['is_create_task'] == 'y';

	if($is_create_task && count($ar_notify))
	{
		if(!class_exists('bitrix24'))
			include $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/xsiteshop/include/class/bitrix24.php';
		$b24 = new bitrix24;

		$hour       = (int)date('G');
		$minute     = (int)date('i');
		$dow        = (int)date('N'); // 1=Пн, 7=Вс
		$time_min   = $hour * 60 + $minute;
		$is_weekday = ($dow >= 1 && $dow <= 5);
		$is_night   = ($time_min >= 1320 || $time_min < 480); // 22:00–07:59

		if ($is_night) {
			$next_dow = ($dow % 7) + 1;
			$group_id = ($next_dow >= 1 && $next_dow <= 5) ? 79 : 9;
		} elseif ($is_weekday && $time_min >= 480 && $time_min <= 988) {
			$group_id = 79; // Будни 08:00–16:28 → администраторы
		} else {
			$group_id = 9;  // Будни 16:29–21:59 или выходные → менеджеры
		}

		$b24->add_task(array(
			'TITLE'       => 'Обновились цены — ' . date('d.m.Y'),
			'DESCRIPTION' => implode("\n", $ar_notify),
			'GROUP_ID'    => $group_id,
		), 5593);
	}
}

if(isset($xs_filter['search']) && !empty($xs_filter['search'])) // фильтр "Поиск"
{
	$where[] = "(
		c.`name` LIKE '%".$xs_filter['search']."%' OR
		c.`id` = '".$xs_filter['search']."'
	)";
	$setFilter = true;
}

if(isset($xs_filter['category_id']) && !empty($xs_filter['category_id']) && (int)$xs_filter['category_id'] > 0) // фильтр "Категория"
{
	$ar_categories = [$xs_filter['category_id']];

	$categories = get_store_subcategories_all($xs_filter['category_id']);

	foreach($categories as $v)
		$ar_categories[] = $v->id;

	$where[] = "c.`category_id` IN ('".implode("','", $ar_categories)."')";
	$setFilter = true;
}

if(count($where) > 0)
	$where = ' WHERE '.implode(' AND ', $where);
else
	$where = '';

$xs_query = "
	SELECT 
		c.*,
		cc.`name` `category_name`
	FROM `xsite_store_components` AS c
	LEFT JOIN `xsite_store_categories` cc ON cc.`id` = c.`category_id`
	".$where;

$xs_data->result = $wpdb->get_results($xs_query.get_order_limit('c.sort', 'asc', false));

foreach($xs_data->result as $k => $v)
	$xs_data->result[$k]->sale_rules = json_decode($v->sale_rules, true);

$big_data['category_list'] = get_store_categories();

$big_data['category_list_all'] = [];

foreach($big_data['category_list'] as $v)
	$big_data['category_list_all'][$v->id] = $v->fullname;

asort($big_data['category_list_all']);

$big_data['category_list_all'][0] = "- верхний уровень -";
