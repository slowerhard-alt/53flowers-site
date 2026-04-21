<?
global $wpdb, $big_data;

if (isset($_POST['save_ms_markup'])) {
    update_option('xs_ms_price_markup', max(0, (int)$_POST['ms_markup']));
    $xs_good[] = 'Наценка МойСклад обновлена: ' . max(0, (int)$_POST['ms_markup']) . '%';
}

$xs_filter = isset($_GET['filter']) ? xs_format($_GET['filter']) : array();
$setFilter  = false;
$where      = '';

if (isset($_POST['edit_groups']) && $_POST['edit_groups'] == 'y') {

    $count_result   = 0;
    $ar_notify      = array();
    $post_components = xs_format(isset($_POST['g']) ? $_POST['g'] : array());

    foreach ($post_components as $group_id => $data) {

        $forced_price = isset($data['forced_price']) ? (float)$data['forced_price'] : 0;
        $sale_percent = isset($data['sale_percent']) && $data['sale_percent'] !== '' ? (int)$data['sale_percent'] : -1;
        $note = isset($data['note']) ? trim(xs_format($data['note'])) : null;

        $group = get_group_components((int)$group_id);
        if (!$group || empty($group->components)) continue;

        if ($forced_price > 0) {
            $old_fp = isset($group->min_fp) ? (float)$group->min_fp : 0;
            if (abs($old_fp - $forced_price) > 0.01) {
                $changed_by = is_user_logged_in() ? $big_data['current_user']->display_name : 'system';
                $wpdb->query("
                    INSERT INTO xsite_store_group_price_log (group_id, old_price, new_price, changed_by)
                    VALUES ('" . (int)$group_id . "', '" . $old_fp . "', '" . $forced_price . "', '" . addslashes($changed_by) . "')
                ");
            }
        }

        if ($note !== null) {
            $wpdb->query("
                UPDATE xsite_store_groups
                SET note = '" . addslashes($note) . "'
                WHERE id = '" . (int)$group_id . "'
            ");
        }

        $ar_notify_parts = array();
        if ($forced_price > 0)  $ar_notify_parts[] = (float)$forced_price . '₽';
        if ($sale_percent > 0)  $ar_notify_parts[] = '−' . $sale_percent . '%';

        foreach ($group->components as $component) {

            $ar_set = array();

            if ($forced_price > 0)
                $ar_set[] = "`forced_price` = '" . $forced_price . "'";

            if ($sale_percent >= 0) {
                $db_sale_rules = json_decode($component->sale_rules, true);
                if (!is_array($db_sale_rules)) $db_sale_rules = array();
                $db_sale_rules['percent'] = $sale_percent;
                $ar_set[] = "`sale_rules` = '" . addslashes(json_encode($db_sale_rules, JSON_UNESCAPED_UNICODE)) . "'";
            }

            if (count($ar_set)) {
                $wpdb->query("
                    UPDATE `xsite_store_components`
                    SET " . implode(', ', $ar_set) . "
                    WHERE `id` = '" . (int)$component->id . "'
                ");
                $count_result++;
            }
        }

        if (count($ar_notify_parts))
            $ar_notify[] = $group->name . ': ' . implode(' ', $ar_notify_parts);
    }

    $is_send_telegram = isset($_POST['is_send_telegram']) && $_POST['is_send_telegram'] == 'y';
    $is_send_bitrix   = isset($_POST['is_send_bitrix'])   && $_POST['is_send_bitrix']   == 'y';
    $is_create_task   = isset($_POST['is_create_task'])   && $_POST['is_create_task']   == 'y';

    update_price_component(0, false, $is_send_telegram, false);

    if ($is_send_bitrix && count($ar_notify)) {
        if (!class_exists('bitrix24'))
            include $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/xsiteshop/include/class/bitrix24.php';

        $b24 = new bitrix24;
        $msg = '[B]Я поменял цены:[/B]' . "\n" . implode("\n", array_map(function($s) { return '• ' . $s; }, $ar_notify));
        $b24->send_price_message('chat44819', $msg);
    }

    if ($is_create_task && count($ar_notify)) {
        if (!class_exists('bitrix24'))
            include $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/xsiteshop/include/class/bitrix24.php';
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

    if ($count_result > 0)
        $xs_good[] = 'Цены обновлены. Обработано компонентов: ' . $count_result;
    else
        $xs_good[] = 'Ничего не изменено (все поля были пустыми).';
}

if (isset($xs_filter['search']) && !empty($xs_filter['search'])) {
    $where = $wpdb->prepare(
        " WHERE g.`name` LIKE %s OR g.`id` = %d",
        '%' . $xs_filter['search'] . '%',
        (int)$xs_filter['search']
    );
    $setFilter = true;
}

$having = '';
if (!empty($xs_filter['in_stock']) && $xs_filter['in_stock'] == 'y') {
    $having = ' HAVING SUM(c.ms_quantity) > 0';
    $setFilter = true;
}

$where_extra = '';
if (!empty($xs_filter['no_price']) && $xs_filter['no_price'] == 'y') {
    $where_extra = ' AND (min_fp IS NULL OR min_fp = 0)';
    $setFilter = true;
}
if (!empty($xs_filter['is_loss']) && $xs_filter['is_loss'] == 'y') {
    $having .= ($having ? ' AND ' : ' HAVING ') . 'min_fp > 0 AND min_fp < AVG(c.original_price)';
    $setFilter = true;
}

$xs_data = $wpdb->get_results("
    SELECT
        g.id,
        g.name,
        g.sort,
        g.note,
        COUNT(DISTINCT cg.component_id)  AS cnt,
        ROUND(AVG(c.original_price))     AS avg_original_price,
        ROUND(AVG(c.days), 1)            AS avg_days,
        SUM(c.ms_quantity)              AS avg_quantity,
        MIN(c.forced_price)              AS min_fp,
        MAX(c.forced_price)              AS max_fp,
        (
            SELECT MAX(changed_at) FROM xsite_store_group_price_log WHERE group_id = g.id
        ) AS last_price_change,
        (
            SELECT COUNT(*) FROM xsite_store_group_price_log WHERE group_id = g.id
        ) AS price_change_count,
        (
            SELECT c2.sale_rules
            FROM xsite_store_components c2
            INNER JOIN xsite_store_components_to_groups cg2 ON cg2.component_id = c2.id
            WHERE cg2.group_id = g.id
            LIMIT 1
        ) AS sample_sale_rules,
        (
            SELECT ROUND(AVG(cl.price))
            FROM xsite_store_cost_log cl
            INNER JOIN xsite_store_components_to_groups cg3 ON cg3.component_id = cl.component_id
            WHERE cg3.group_id = g.id
            AND cl.recorded_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
            AND cl.recorded_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
        ) AS avg_cost_prev_week
    FROM xsite_store_groups g
    LEFT JOIN xsite_store_components_to_groups cg ON cg.group_id = g.id
    LEFT JOIN xsite_store_components c ON c.id = cg.component_id
    " . $where . "
    GROUP BY g.id
    " . $where_extra . $having . "
    ORDER BY g.sort ASC
");

foreach ($xs_data as $k => $v) {
    $xs_data[$k]->sample_sale_rules = json_decode($v->sample_sale_rules, true);
}

if (!empty($xs_data)) {
    $group_ids_list = array();
    foreach ($xs_data as $v) {
        $group_ids_list[] = (int)$v->id;
    }
    $group_ids_sql = implode(',', $group_ids_list);

    $ms_rows = $wpdb->get_results("
        SELECT cg.group_id, c.ms_components
        FROM xsite_store_components c
        INNER JOIN xsite_store_components_to_groups cg ON cg.component_id = c.id
        WHERE cg.group_id IN (" . $group_ids_sql . ")
        AND c.ms_components != ''
    ");

    $group_ms_qty = array();
    foreach ($ms_rows as $row) {
        $gid = (int)$row->group_id;
        $items = json_decode($row->ms_components, true);
        if (!is_array($items)) continue;
        if (!isset($group_ms_qty[$gid])) $group_ms_qty[$gid] = array();
        foreach ($items as $item) {
            if (empty($item['code'])) continue;
            $code = $item['code'];
            if (!isset($group_ms_qty[$gid][$code])) {
                $group_ms_qty[$gid][$code] = (int)$item['quantity'];
            }
        }
    }

    foreach ($xs_data as $k => $v) {
        $gid = (int)$v->id;
        if (isset($group_ms_qty[$gid])) {
            $xs_data[$k]->avg_quantity = array_sum($group_ms_qty[$gid]);
        }
    }
}

$xs_total_groups = count($xs_data);
$xs_priced_groups = 0;
foreach ($xs_data as $v) {
    if (!empty($v->min_fp) && $v->min_fp > 0) $xs_priced_groups++;
}

global $xs_unassigned_count;
$xs_unassigned_count = (int)$wpdb->get_var("
    SELECT COUNT(*)
    FROM xsite_store_components c
    LEFT JOIN xsite_store_components_to_groups cg ON cg.component_id = c.id
    WHERE cg.group_id IS NULL
    AND c.category_id IN (
        SELECT id FROM xsite_store_categories
        WHERE (parent_id = 12 OR id = 12)
        AND id != 57
    )
");
