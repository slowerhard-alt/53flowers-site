<?php

$pass = 0; $fail = 0;

function xs_test($name, $result, $expected = true) {
    global $pass, $fail;
    if ($result === $expected) {
        echo "PASS: $name\n";
        $pass++;
    } else {
        echo "FAIL: $name (got: " . var_export($result, true) . " expected: " . var_export($expected, true) . ")\n";
        $fail++;
    }
}

global $wpdb;

$exists = $wpdb->get_var("SHOW TABLES LIKE 'xsite_store_groups'");
xs_test('xsite_store_groups table exists', $exists === 'xsite_store_groups');

$cols = $wpdb->get_col("SHOW COLUMNS FROM xsite_store_groups");
xs_test('xsite_store_groups has note column', in_array('note', $cols));

$log_exists = $wpdb->get_var("SHOW TABLES LIKE 'xsite_store_group_price_log'");
xs_test('xsite_store_group_price_log table exists', $log_exists === 'xsite_store_group_price_log');

$wpdb->query("INSERT INTO xsite_store_groups (name, sort, note) VALUES ('__TEST_GROUP__', 9999, 'тест')");
$test_id = (int)$wpdb->insert_id;
xs_test('test group created', $test_id > 0);

$note = $wpdb->get_var("SELECT note FROM xsite_store_groups WHERE id = $test_id");
xs_test('note field saved correctly', $note === 'тест');

$wpdb->query("INSERT INTO xsite_store_group_price_log (group_id, old_price, new_price, changed_by) VALUES ($test_id, 0, 500, 'test')");
$log_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM xsite_store_group_price_log WHERE group_id = $test_id");
xs_test('price log entry created', $log_count === 1);

$wpdb->query("INSERT INTO xsite_store_groups (name, sort) VALUES ('__TEST_GROUP_2__', 9998)");
$test_id2 = (int)$wpdb->insert_id;
$wpdb->query("UPDATE xsite_store_groups SET sort = 9998 WHERE id = $test_id");
$wpdb->query("UPDATE xsite_store_groups SET sort = 9999 WHERE id = $test_id2");
$new_sort = (int)$wpdb->get_var("SELECT sort FROM xsite_store_groups WHERE id = $test_id");
xs_test('sort swap simulation works', $new_sort === 9998);

$wpdb->query("DELETE FROM xsite_store_groups WHERE id IN ($test_id, $test_id2)");
$wpdb->query("DELETE FROM xsite_store_group_price_log WHERE group_id = $test_id");

echo "\n=== Результат: $pass PASS, $fail FAIL ===\n";
