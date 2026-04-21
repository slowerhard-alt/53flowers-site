<?
// ============================================================
// Группы коэффициентов — правила наценки по диапазонам дней
// на складе (1–3·k₁ / 4–5·k₂ / ... / 8+·kₙ).
// Привязка к компоненту: xsite_store_components.coefficient_group_id
// Дефолт-коэф для компонентов без группы: xs_default_coefficient
// ============================================================

function add_coefficient_group($arg = [])
{
	global $wpdb;

	if(!isset($arg['name']) || empty(trim($arg['name'])))
		return ['status' => 'error', 'message' => "Не заполнено обязательное поле Название"];

	$wpdb->query("
		INSERT INTO `xsite_store_coefficient_groups`
		SET
			`name` = '".$wpdb->_real_escape(trim($arg['name']))."',
			`color` = '".$wpdb->_real_escape(isset($arg['color']) ? $arg['color'] : '')."',
			`sort` = '".(isset($arg['sort']) && is_numeric($arg['sort']) ? (int)$arg['sort'] : 0)."'
	");

	if(!($insert_id = $wpdb->insert_id))
		return ['status' => 'error', 'message' => "Ошибка при создании группы коэффициентов"];

	return [
		'status' => 'good',
		'message' => "Группа коэффициентов добавлена",
		'insert_id' => $insert_id
	];
}

function get_coefficient_group($id, $with_tiers = true)
{
	global $wpdb;

	$id = (int)$id;
	if($id <= 0)
		return false;

	$row = $wpdb->get_row("SELECT * FROM `xsite_store_coefficient_groups` WHERE `id` = '".$id."'");
	if(!$row)
		return false;

	$row->tiers = [];
	$row->components_count = (int)$wpdb->get_var(
		"SELECT COUNT(*) FROM `xsite_store_components` WHERE `coefficient_group_id` = '".$id."'"
	);

	if($with_tiers)
	{
		$tiers = $wpdb->get_results("
			SELECT * FROM `xsite_store_coefficient_tiers`
			WHERE `coefficient_group_id` = '".$id."'
			ORDER BY `min_days` ASC
		");
		$row->tiers = $tiers ? $tiers : [];
	}

	return $row;
}

function get_all_coefficient_groups($with_tiers = false)
{
	global $wpdb;

	$rows = $wpdb->get_results("SELECT * FROM `xsite_store_coefficient_groups` ORDER BY `sort` ASC, `id` ASC");
	if(!$rows)
		return [];

	$result = [];

	if($with_tiers)
	{
		$all_tiers = $wpdb->get_results("
			SELECT * FROM `xsite_store_coefficient_tiers` ORDER BY `coefficient_group_id`, `min_days` ASC
		");
		$tiers_by_group = [];
		if($all_tiers)
			foreach($all_tiers as $t)
				$tiers_by_group[$t->coefficient_group_id][] = $t;

		$counts = $wpdb->get_results("
			SELECT `coefficient_group_id`, COUNT(*) AS cnt
			FROM `xsite_store_components`
			WHERE `coefficient_group_id` > 0
			GROUP BY `coefficient_group_id`
		");
		$count_by_group = [];
		if($counts)
			foreach($counts as $c)
				$count_by_group[$c->coefficient_group_id] = (int)$c->cnt;
	}

	foreach($rows as $r)
	{
		if($with_tiers)
		{
			$r->tiers = isset($tiers_by_group[$r->id]) ? $tiers_by_group[$r->id] : [];
			$r->components_count = isset($count_by_group[$r->id]) ? $count_by_group[$r->id] : 0;
		}
		$result[] = $r;
	}

	return $result;
}

function edit_coefficient_group($arg)
{
	global $wpdb;

	if(!isset($arg['id']) || !($row = get_coefficient_group((int)$arg['id'], false)))
		return ['status' => 'error', 'message' => "Группа коэффициентов не найдена"];

	if(!isset($arg['name']) || empty(trim($arg['name'])))
		return ['status' => 'error', 'message' => "Не заполнено обязательное поле Название"];

	$wpdb->query("
		UPDATE `xsite_store_coefficient_groups`
		SET
			`name` = '".$wpdb->_real_escape(trim($arg['name']))."',
			`color` = '".$wpdb->_real_escape(isset($arg['color']) ? $arg['color'] : '')."',
			`sort` = '".(isset($arg['sort']) && is_numeric($arg['sort']) ? (int)$arg['sort'] : 0)."'
		WHERE `id` = '".$row->id."'
	");

	return ['status' => 'good', 'message' => "Группа коэффициентов обновлена"];
}

function del_coefficient_group($id)
{
	global $wpdb;

	if(!($row = get_coefficient_group((int)$id, false)))
		return ['status' => 'error', 'message' => "Группа коэффициентов не найдена"];

	if($row->components_count > 0)
	{
		return [
			'status' => 'error',
			'message' => "Группа используется ".$row->components_count." компонентами. Отвяжите их перед удалением."
		];
	}

	$wpdb->query("DELETE FROM `xsite_store_coefficient_groups` WHERE `id` = '".$row->id."'");
	$wpdb->query("DELETE FROM `xsite_store_coefficient_tiers` WHERE `coefficient_group_id` = '".$row->id."'");

	return ['status' => 'good', 'message' => "Группа коэффициентов удалена"];
}

// Валидирует и сохраняет все tier-ы группы через DELETE + INSERT.
// $tiers — массив [[min_days, max_days, k, color], ...]
function save_coefficient_group_tiers($group_id, $tiers)
{
	global $wpdb, $ar_coefficient;

	$group_id = (int)$group_id;
	if($group_id <= 0 || !get_coefficient_group($group_id, false))
		return ['status' => 'error', 'message' => "Группа коэффициентов не найдена"];

	$clean = [];
	$null_seen = 0;

	if(is_array($tiers))
	{
		foreach($tiers as $t)
		{
			$min = isset($t['min_days']) && is_numeric($t['min_days']) ? (int)$t['min_days'] : 0;
			$max_raw = isset($t['max_days']) ? trim($t['max_days']) : '';
			$max = ($max_raw === '') ? null : (int)$max_raw;
			$k = isset($t['k']) && is_numeric($t['k']) ? (float)$t['k'] : 0;
			$color = isset($t['color']) ? trim($t['color']) : '';

			if($min < 1)
				return ['status' => 'error', 'message' => "Минимальное значение 'от' — 1 день"];

			if($max !== null && $max < $min)
				return ['status' => 'error', 'message' => "Значение 'до' не может быть меньше 'от' (строка min=".$min.", max=".$max.")"];

			if($k <= 0)
				return ['status' => 'error', 'message' => "Коэффициент должен быть положительным"];

			if($max === null)
				$null_seen++;

			$clean[] = ['min' => $min, 'max' => $max, 'k' => $k, 'color' => $color];
		}
	}

	if($null_seen > 1)
		return ['status' => 'error', 'message' => "Только один диапазон может иметь 'до = ∞'"];

	// Сортировка по min_days
	usort($clean, function($a, $b){ return $a['min'] - $b['min']; });

	// Проверка на перекрытия
	for($i = 0; $i < count($clean) - 1; $i++)
	{
		$a = $clean[$i];
		$b = $clean[$i + 1];
		$a_max = ($a['max'] === null) ? PHP_INT_MAX : $a['max'];
		if($a_max >= $b['min'])
			return ['status' => 'error', 'message' => "Диапазоны перекрываются: [".$a['min'].'–'.($a['max']===null?'∞':$a['max'])."] и [".$b['min'].'–'.($b['max']===null?'∞':$b['max'])."]"];
	}

	$wpdb->query("DELETE FROM `xsite_store_coefficient_tiers` WHERE `coefficient_group_id` = '".$group_id."'");

	$sort = 0;
	foreach($clean as $t)
	{
		$max_sql = ($t['max'] === null) ? "NULL" : "'".$t['max']."'";
		$wpdb->query("
			INSERT INTO `xsite_store_coefficient_tiers`
			SET
				`coefficient_group_id` = '".$group_id."',
				`min_days` = '".$t['min']."',
				`max_days` = ".$max_sql.",
				`k` = '".$t['k']."',
				`color` = '".$wpdb->_real_escape($t['color'])."',
				`sort` = '".$sort."'
		");
		$sort++;
	}

	// Сброс кеша
	$ar_coefficient = [];

	return ['status' => 'good', 'message' => "Диапазоны сохранены"];
}

// Получить список ID компонентов для пересчёта.
// $group_id == 0 → все компоненты; >0 → только из этой группы коэф.
function get_recompute_component_ids($group_id)
{
	global $wpdb;

	$group_id = (int)$group_id;

	if($group_id == 0)
		$ids = $wpdb->get_col("SELECT `id` FROM `xsite_store_components` ORDER BY `id` ASC");
	else
		$ids = $wpdb->get_col("SELECT `id` FROM `xsite_store_components` WHERE `coefficient_group_id` = '".$group_id."' ORDER BY `id` ASC");

	return is_array($ids) ? array_map('intval', $ids) : [];
}

// Пересчитать цены указанного списка компонентов (batch).
// Максимум 200 ID за вызов для безопасности по таймауту.
function recompute_prices_batch($ids)
{
	global $wpdb, $ar_coefficient;

	$ar_coefficient = [];

	set_time_limit(120);
	ignore_user_abort(true);

	if(!is_array($ids) || count($ids) == 0)
		return ['status' => 'good', 'processed' => 0];

	$ids = array_slice(array_map('intval', $ids), 0, 200);

	$processed = 0;
	foreach($ids as $cid)
	{
		if($cid <= 0) continue;
		update_price_component($cid, true, false, false);
		$processed++;
	}

	return ['status' => 'good', 'processed' => $processed];
}

// Пересчитать цены компонентов одной группы (синхронный режим, оставлено для API-совместимости).
// $group_id == 0 → все компоненты системы (bulk).
function recompute_prices_for_coefficient_group($group_id)
{
	$ids = get_recompute_component_ids($group_id);
	if(count($ids) == 0)
		return ['status' => 'good', 'message' => ($group_id > 0 ? "В группе нет компонентов" : "Компонентов нет")];

	set_time_limit(600);
	ignore_user_abort(true);

	global $ar_coefficient;
	$ar_coefficient = [];

	$count = 0;
	foreach($ids as $cid)
	{
		update_price_component((int)$cid, true, false, false);
		$count++;
	}

	return ['status' => 'good', 'message' => "Пересчитано компонентов: ".$count];
}

// Собрать короткое описание диапазонов для таблицы списка.
// Пример: "1–3·2.2 · 4–5·2.0 · 6+·1.8"
function format_coefficient_group_tiers($tiers)
{
	if(empty($tiers))
		return "<em>нет диапазонов</em>";

	$parts = [];
	foreach($tiers as $t)
	{
		$range = $t->max_days === null
			? $t->min_days.'+'
			: $t->min_days.'–'.$t->max_days;
		$parts[] = $range.'·'.rtrim(rtrim((string)$t->k, '0'), '.');
	}
	return implode(' · ', $parts);
}

// Поставить поле coefficient_group_id одному компоненту.
function set_component_coefficient_group($component_id, $coefficient_group_id)
{
	global $wpdb;

	$component_id = (int)$component_id;
	$coefficient_group_id = (int)$coefficient_group_id;

	if($component_id <= 0)
		return false;

	if($coefficient_group_id > 0 && !get_coefficient_group($coefficient_group_id, false))
		return false;

	$wpdb->query("
		UPDATE `xsite_store_components`
		SET `coefficient_group_id` = '".$coefficient_group_id."'
		WHERE `id` = '".$component_id."'
	");

	return true;
}

// Источник цены для отображения в колонке "Источник" (section=report).
// Возвращает ['label' => '...', 'source' => 'forced|group|default'].
function get_component_price_source($component)
{
	if(!is_object($component))
		return ['label' => '', 'source' => ''];

	if(isset($component->forced_price) && (float)$component->forced_price > 0)
		return ['label' => 'П.цена', 'source' => 'forced'];

	if(isset($component->coefficient_group_id) && (int)$component->coefficient_group_id > 0)
	{
		$g = get_coefficient_group((int)$component->coefficient_group_id, false);
		if($g)
			return ['label' => 'Группа: '.$g->name, 'source' => 'group'];
	}

	$default_k = (float)xs_get_option('xs_default_coefficient');
	if($default_k <= 0) $default_k = 2.5;
	return ['label' => 'Дефолт '.rtrim(rtrim((string)$default_k, '0'), '.'), 'source' => 'default'];
}

?>
