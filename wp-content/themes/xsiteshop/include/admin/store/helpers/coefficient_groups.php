<?
// Helper для раздела admin.php?page=store&section=coefficient_groups

global $wpdb, $big_data, $xs_good, $xs_error, $cg_list;

// Сохранение глобальной настройки дефолт-коэффициента
if (isset($_POST['save_default_coefficient']) && $_POST['save_default_coefficient'] == 'y') {
    $val = isset($_POST['default_coefficient']) && is_numeric($_POST['default_coefficient'])
        ? (float)$_POST['default_coefficient']
        : 0;
    if ($val > 0) {
        update_option('xs_default_coefficient', (string)$val);
        $xs_good[] = 'Дефолт-коэффициент сохранён: ' . $val;
    } else {
        $xs_error[] = 'Коэффициент должен быть положительным';
    }
}

// Загрузка списка групп с tier-ами и счётчиком компонентов
$cg_list = get_all_coefficient_groups(true);
