<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

global $big_data, $wpdb;

if(!current_user_can('administrator'))
{
	global $post, $page, $wp_query;
	$wp_query->set_404();
	header("HTTP/1.1 404 Not Found");
	get_template_part('404');
	die();
}

$action = xs_format($_REQUEST['action']);
$ar_result = [];

$post_data = isset($_POST['post_data']) && is_array($_POST['post_data'])
	? xs_format($_POST['post_data'])
	: [];

$get_data = isset($_GET) && is_array($_GET)
	? xs_format($_GET)
	: [];

if($action == "group-add")
{
	$result = add_group_components($post_data);

	if($result['status'] == 'error')
	{
		ob_start();

		message($result['message'], $result['status']);
		xs_get_message();

		$code = ob_get_contents();
		ob_end_clean();

		$ar_result['html'] = $code;
	}
	else
	{
		message($result['message'], $result['status']);
		$ar_result['fancybox'] = "/wp-content/themes/xsiteshop/load/admin/modal/index.php?id=".(int)$result['insert_id']."&modal=group-detail";
	}
}

if($action == "group-edit")
{

	if(isset($_POST['is_delete']) && $_POST['is_delete'] == 'y' && isset($post_data['id']) && !empty($post_data['id']))
	{
		$result = del_group_components($post_data['id']);

		if($result['status'] == 'good')
		{
			message($result['message'], $result['status']);
			$ar_result['href'] = "/wp-admin/admin.php?page=store&section=group";
		}
		else
		{
			ob_start();

			message($result['message'], $result['status']);
			xs_get_message();

			$code = ob_get_contents();
			ob_end_clean();

			$ar_result['html'] = $code;
		}
	}
	else
	{
		$result = edit_group_components($post_data);

		ob_start();

		message($result['message'], $result['status']);
		xs_get_message();

		$code = ob_get_contents();
		ob_end_clean();

		$ar_result['html'] = $code;
	}
}

if($action == "group-components-add")
{
	$result = add_components_to_group($post_data);

	if($result['status'] == 'error')
	{
		ob_start();

		message($result['message'], $result['status']);
		xs_get_message();

		$code = ob_get_contents();
		ob_end_clean();

		$ar_result['html'] = $code;
	}
	else
	{
		message($result['message'], $result['status']);
		$ar_result['fancybox'] = "/wp-content/themes/xsiteshop/load/admin/modal/index.php?id=".(int)$post_data['group_id']."&modal=group-detail";
	}
}

if($action == "group-components-del")
{
	$ar_result = del_components_to_group($get_data);
}

if($action == "group-components-del-all")
{
	$result = del_all_components_to_group($post_data['id']);

	if($result['status'] == 'error')
	{
		ob_start();

		message($result['message'], $result['status']);
		xs_get_message();

		$code = ob_get_contents();
		ob_end_clean();

		$ar_result['html'] = $code;
	}
	else
	{
		message($result['message'], $result['status']);
		$ar_result['fancybox'] = "/wp-content/themes/xsiteshop/load/admin/modal/index.php?id=".(int)$post_data['id']."&modal=group-detail";
	}
}

if($action == "group-auto-assign")
{
	$dry_run = !(isset($_POST['dry_run']) && $_POST['dry_run'] == 'n');

	$groups = $wpdb->get_results("SELECT id, name FROM xsite_store_groups ORDER BY sort ASC");

	$preview_groups = array();
	$total          = 0;

	foreach($groups as $g)
	{
		$gname = $wpdb->esc_like($g->name);
		$sql   = $wpdb->prepare(
			"SELECT id, name FROM xsite_store_components
			 WHERE name LIKE %s
			   AND id NOT IN (SELECT component_id FROM xsite_store_components_to_groups)
			 ORDER BY name ASC",
			'%' . $gname . '%'
		);

		$rows  = $wpdb->get_results($sql);
		$count = count($rows);

		if($count > 0)
		{
			$names = array();
			foreach($rows as $r)
				$names[] = $r->name;

			$preview_groups[] = array(
				'group'  => $g->name,
				'count'  => $count,
				'names'  => array_slice($names, 0, 15),
				'ids'    => array_map(function($r){ return (int)$r->id; }, $rows),
			);
			$total += $count;
		}
	}

	if(!$dry_run && $total > 0)
	{
		$inserted = 0;
		foreach($groups as $g)
		{
			$gname      = $wpdb->esc_like($g->name);
			$components = $wpdb->get_results($wpdb->prepare(
				"SELECT id FROM xsite_store_components
				 WHERE name LIKE %s
				   AND id NOT IN (SELECT component_id FROM xsite_store_components_to_groups)",
				'%' . $gname . '%'
			));
			foreach($components as $c)
			{
				$wpdb->query("
					INSERT INTO xsite_store_components_to_groups
					SET component_id = '" . (int)$c->id . "', group_id = '" . (int)$g->id . "'
				");
				$inserted++;
			}
		}
		$ar_result['status']  = 'good';
		$ar_result['message'] = 'Привязано компонентов: ' . $inserted;
	}
	else
	{
		$ar_result['status'] = 'preview';
		$ar_result['groups'] = $preview_groups;
		$ar_result['total']  = $total;
	}
}

if($action == "group-sort-swap")
{
	$group_id  = (int)xs_format(isset($_POST['group_id']) ? $_POST['group_id'] : 0);
	$direction = xs_format(isset($_POST['direction']) ? $_POST['direction'] : '');

	if ($group_id > 0 && in_array($direction, array('up', 'down'))) {

		$current = $wpdb->get_row("SELECT id, sort FROM `xsite_store_groups` WHERE id = '" . $group_id . "' LIMIT 1");

		if ($current) {
			if ($direction == 'up') {
				$neighbor = $wpdb->get_row("
					SELECT id, sort FROM `xsite_store_groups`
					WHERE sort < '" . (int)$current->sort . "'
					ORDER BY sort DESC
					LIMIT 1
				");
			} else {
				$neighbor = $wpdb->get_row("
					SELECT id, sort FROM `xsite_store_groups`
					WHERE sort > '" . (int)$current->sort . "'
					ORDER BY sort ASC
					LIMIT 1
				");
			}

			if ($neighbor) {
				$wpdb->query("UPDATE `xsite_store_groups` SET sort = '" . (int)$neighbor->sort . "' WHERE id = '" . (int)$current->id . "'");
				$wpdb->query("UPDATE `xsite_store_groups` SET sort = '" . (int)$current->sort . "' WHERE id = '" . (int)$neighbor->id . "'");
				$ar_result['status'] = 'ok';
			} else {
				$ar_result['status'] = 'ok';
			}
		} else {
			$ar_result['status'] = 'error';
			$ar_result['message'] = 'Группа не найдена';
		}
	}
}

if($action == "group-get-components")
{
	$group_id = (int)xs_format(isset($_POST['group_id']) ? $_POST['group_id'] : 0);
	if ($group_id > 0) {
		$rows = $wpdb->get_results($wpdb->prepare(
			"SELECT c.name FROM xsite_store_components c
			 INNER JOIN xsite_store_components_to_groups cg ON cg.component_id = c.id
			 WHERE cg.group_id = %d ORDER BY c.name ASC",
			$group_id
		));
		$names = array_map(function($r){ return $r->name; }, $rows);
		$count = count($names);
		$ar_result['status'] = 'ok';
		$ar_result['count']  = $count;
		$ar_result['names']  = array_slice($names, 0, 20);
		$ar_result['more']   = max(0, $count - 20);
	} else {
		$ar_result['status'] = 'error';
	}
}

if($action == "group-rename")
{
	$group_id = (int)xs_format(isset($_POST['group_id']) ? $_POST['group_id'] : 0);
	$name     = trim(xs_format(isset($_POST['name']) ? $_POST['name'] : ''));
	if ($group_id > 0 && !empty($name)) {
		$wpdb->query($wpdb->prepare(
			"UPDATE xsite_store_groups SET name = %s WHERE id = %d",
			$name,
			$group_id
		));
		$ar_result['status'] = 'ok';
	} else {
		$ar_result['status']  = 'error';
		$ar_result['message'] = 'Некорректные данные';
	}
}

if($action == "group-save-note")
{
	$group_id = (int)xs_format(isset($_POST['group_id']) ? $_POST['group_id'] : 0);
	$note     = xs_format(isset($_POST['note']) ? $_POST['note'] : '');
	if ($group_id > 0) {
		$wpdb->query($wpdb->prepare(
			"UPDATE xsite_store_groups SET note = %s WHERE id = %d",
			$note,
			$group_id
		));
		$ar_result['status'] = 'ok';
	} else {
		$ar_result['status'] = 'error';
	}
}

if($action == "group-price-log")
{
	$group_id = (int)xs_format(isset($_POST['group_id']) ? $_POST['group_id'] : 0);
	if ($group_id > 0) {
		$rows = $wpdb->get_results($wpdb->prepare(
			"SELECT old_price, new_price, changed_by,
			        DATE_FORMAT(changed_at, '%%d.%%m.%%Y %%H:%%i') AS changed_at
			 FROM xsite_store_group_price_log
			 WHERE group_id = %d ORDER BY changed_at DESC LIMIT 20",
			$group_id
		));
		$ar_result['status'] = 'ok';
		$ar_result['rows']   = $rows;
	} else {
		$ar_result['status'] = 'error';
	}
}

// ============================================================
// Группы коэффициентов (правила наценки по дням на складе)
// ============================================================

if($action == "coefficient-group-add")
{
	$result = add_coefficient_group($post_data);

	if($result['status'] == 'error')
	{
		ob_start();
		message($result['message'], $result['status']);
		xs_get_message();
		$code = ob_get_contents();
		ob_end_clean();
		$ar_result['html'] = $code;
	}
	else
	{
		message($result['message'], $result['status']);
		$ar_result['fancybox'] = "/wp-content/themes/xsiteshop/load/admin/modal/index.php?id=".(int)$result['insert_id']."&modal=coefficient-group-detail";
	}
}

if($action == "coefficient-group-edit")
{
	if(isset($_POST['is_delete']) && $_POST['is_delete'] == 'y' && isset($post_data['id']) && !empty($post_data['id']))
	{
		$result = del_coefficient_group((int)$post_data['id']);

		if($result['status'] == 'good')
		{
			message($result['message'], $result['status']);
			$ar_result['href'] = "/wp-admin/admin.php?page=store&section=coefficient_groups";
		}
		else
		{
			ob_start();
			message($result['message'], $result['status']);
			xs_get_message();
			$code = ob_get_contents();
			ob_end_clean();
			$ar_result['html'] = $code;
		}
	}
	else
	{
		$result = edit_coefficient_group($post_data);
		ob_start();
		message($result['message'], $result['status']);
		xs_get_message();
		$code = ob_get_contents();
		ob_end_clean();
		$ar_result['html'] = $code;
	}
}

if($action == "coefficient-group-del")
{
	$id = (int)xs_format(isset($_POST['id']) ? $_POST['id'] : 0);
	$result = del_coefficient_group($id);
	$ar_result = $result;
}

if($action == "coefficient-group-tiers-save")
{
	$gid = isset($post_data['group_id']) ? (int)$post_data['group_id'] : 0;
	$tiers = isset($post_data['tiers']) && is_array($post_data['tiers'])
		? array_values($post_data['tiers'])
		: [];
	$result = save_coefficient_group_tiers($gid, $tiers);
	$ar_result = $result;
}

if($action == "coefficient-group-components-add")
{
	$gid = isset($post_data['group_id']) ? (int)$post_data['group_id'] : 0;
	$components = isset($post_data['components']) && is_array($post_data['components'])
		? $post_data['components']
		: [];

	if($gid <= 0 || !get_coefficient_group($gid, false))
	{
		ob_start();
		message("Группа коэффициентов не найдена", 'error');
		xs_get_message();
		$code = ob_get_contents();
		ob_end_clean();
		$ar_result['html'] = $code;
	}
	elseif(count($components) == 0)
	{
		ob_start();
		message("Не выбраны компоненты", 'error');
		xs_get_message();
		$code = ob_get_contents();
		ob_end_clean();
		$ar_result['html'] = $code;
	}
	else
	{
		$cnt = 0;
		foreach($components as $component_id => $val)
		{
			if($val !== 'y') continue;
			if(set_component_coefficient_group((int)$component_id, $gid))
				$cnt++;
		}
		message("Привязано компонентов: ".$cnt, 'good');
		$ar_result['fancybox'] = "/wp-content/themes/xsiteshop/load/admin/modal/index.php?id=".$gid."&modal=coefficient-group-detail";
	}
}

if($action == "coefficient-group-components-del")
{
	$component_id = (int)xs_format(isset($_POST['component_id']) ? $_POST['component_id'] : 0);
	if($component_id > 0 && set_component_coefficient_group($component_id, 0))
		$ar_result = ['status' => 'good', 'message' => 'Компонент отвязан от группы'];
	else
		$ar_result = ['status' => 'error', 'message' => 'Не удалось отвязать компонент'];
}

if($action == "coefficient-group-recompute")
{
	$gid = (int)xs_format(isset($_POST['group_id']) ? $_POST['group_id'] : 0);
	$result = recompute_prices_for_coefficient_group($gid);
	$ar_result = $result;
}

if($action == "coefficient-group-recompute-all")
{
	$result = recompute_prices_for_coefficient_group(0);
	$ar_result = $result;
}

if($action == "coefficient-group-recompute-list")
{
	$gid = (int)xs_format(isset($_POST['group_id']) ? $_POST['group_id'] : 0);
	$ids = get_recompute_component_ids($gid);
	$ar_result = [
		'status' => 'good',
		'ids' => $ids,
		'total' => count($ids),
	];
}

if($action == "coefficient-group-recompute-batch")
{
	$raw = isset($_POST['ids']) ? $_POST['ids'] : [];
	$ids = is_array($raw) ? array_map('intval', $raw) : [];
	$result = recompute_prices_batch($ids);
	$ar_result = $result;
}

if(count($ar_result) == 0)
{
	global $post, $page, $wp_query;
	$wp_query->set_404();
	header("HTTP/1.1 404 Not Found");
	get_template_part('404');
	die();
}
else
{
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($ar_result, JSON_UNESCAPED_UNICODE);
}
