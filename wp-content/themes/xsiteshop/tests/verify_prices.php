<?php
/**
 * Скрипт верификации цен между Beget (53flowers.com) и Timeweb (cvetyru-vn.ru)
 *
 * Запуск (из корня WordPress на Beget):
 *   wp eval-file wp-content/themes/xsiteshop/tests/verify_prices.php
 *   wp eval-file wp-content/themes/xsiteshop/tests/verify_prices.php -- --component_id=123
 *   wp eval-file wp-content/themes/xsiteshop/tests/verify_prices.php -- --limit=5
 */

if (!defined('ABSPATH')) {
    echo "Run via WP-CLI: wp eval-file ...\n";
    exit(1);
}

global $wpdb, $wpdb53;

// --- Параметры запуска ---
$args = array();
if (isset($GLOBALS['argv'])) {
    foreach ($GLOBALS['argv'] as $arg) {
        if (strpos($arg, '--') === 0) {
            $parts = explode('=', ltrim($arg, '-'), 2);
            if (count($parts) == 2) {
                $args[$parts[0]] = $parts[1];
            }
        }
    }
}

$specific_component_id = isset($args['component_id']) ? (int)$args['component_id'] : 0;
$limit = isset($args['limit']) ? (int)$args['limit'] : 10;

// --- Проверка Beget DB ---
if (!$wpdb53) {
    echo "[ERROR] Beget DB (wpdb53) недоступна\n";
    exit(1);
}

// --- Получаем компоненты ---
if ($specific_component_id > 0) {
    $components = $wpdb53->get_results(
        "SELECT id, name, price FROM xsite_store_components WHERE id = '" . $specific_component_id . "'"
    );
} else {
    $components = $wpdb53->get_results(
        "SELECT id, name, price FROM xsite_store_components ORDER BY id DESC LIMIT " . $limit
    );
}

if (!$components) {
    echo "[ERROR] Компоненты не найдены\n";
    exit(1);
}

echo "\n";
echo str_repeat("=", 90) . "\n";
echo "ВЕРИФИКАЦИЯ ЦЕН\n";
echo "Дата: " . date("Y-m-d H:i:s") . "\n";
echo "Компонентов для проверки: " . count($components) . "\n";
echo str_repeat("=", 90) . "\n\n";

// --- Шапка таблицы ---
printf("%-6s %-30s %10s %12s %12s\n",
    "ID", "Компонент", "Цена(Beget)", "Beget OK", "Timeweb OK"
);
echo str_repeat("-", 75) . "\n";

$total_ok = 0;
$total_fail = 0;
$timeweb_issues = array();

foreach ($components as $c) {
    $component_id = (int)$c->id;
    $component_price = (float)$c->price;

    // --- Получаем товары Beget для этого компонента ---
    $beget_products = $wpdb53->get_results(
        "SELECT product_id FROM xsite_store_products WHERE component_id = '" . $component_id . "' AND site = '53flowers'"
    );

    // --- Получаем товары Timeweb для этого компонента ---
    $tw_products = $wpdb53->get_results(
        "SELECT product_id FROM xsite_store_products WHERE component_id = '" . $component_id . "' AND site = 'cvetyru-vn'"
    );

    // --- Проверка Beget: сумма компонентов в _price товара ---
    $beget_ok = 0;
    $beget_fail = 0;

    if ($beget_products) {
        foreach ($beget_products as $p) {
            $product_id = (int)$p->product_id;
            // Получаем текущую цену товара на Beget из wp_postmeta
            $wc_price = (float)$wpdb->get_var(
                "SELECT meta_value FROM wp_postmeta WHERE post_id = '" . $product_id . "' AND meta_key = '_price'"
            );
            // Получаем все компоненты товара и их цены
            $all_comps = $wpdb53->get_results(
                "SELECT sp.component_id, sc.price, sp.quantity
                 FROM xsite_store_products sp
                 LEFT JOIN xsite_store_components sc ON sc.id = sp.component_id
                 WHERE sp.product_id = '" . $product_id . "' AND sp.site = '53flowers'"
            );
            if ($all_comps) {
                $calc_price = 0;
                foreach ($all_comps as $ac) {
                    $calc_price += (float)$ac->price * (int)$ac->quantity;
                }
                $calc_price = round($calc_price);
                $wc_price_r = round($wc_price);
                // Допускаем расхождение до 5 руб (округления)
                if (abs($calc_price - $wc_price_r) <= 5) {
                    $beget_ok++;
                } else {
                    $beget_fail++;
                }
            }
        }
    }

    $beget_status = "N/A";
    if (count($beget_products) > 0) {
        $beget_status = ($beget_fail == 0) ? "OK(" . $beget_ok . ")" : "FAIL(" . $beget_fail . "/" . count($beget_products) . ")";
    }

    // --- Timeweb: генерируем команду WP-CLI для ручной проверки ---
    $tw_product_ids = array();
    if ($tw_products) {
        foreach ($tw_products as $p) {
            $tw_product_ids[] = (int)$p->product_id;
        }
    }

    $tw_status = "N/A";
    if (count($tw_product_ids) > 0) {
        // Проверяем через SSH + WP-CLI на Timeweb
        $tw_ids_str = implode(",", $tw_product_ids);
        $ssh_cmd = "SSHPASS='eJF6qQq@A*Jud1' sshpass -e ssh -o StrictHostKeyChecking=no -o PreferredAuthentications=password -o PubkeyAuthentication=no root@85.193.87.217 "
            . "\"wp --path=/var/www/fastuser/data/www/cvetyru-vn.ru eval "
            . "'global \\\$wpdb; \\\$ids=array(" . $tw_ids_str . "); foreach(\\\$ids as \\\$id){ \\\$p=wc_get_product(\\\$id); if(\\\$p) echo \\\$id.\\\":\\\".wc_get_price_to_display(\\\$p).\\\" \\\"; }'\"";

        $tw_result = @shell_exec($ssh_cmd . " 2>/dev/null");
        if ($tw_result) {
            $tw_prices = explode(" ", trim($tw_result));
            $tw_ok = 0;
            $tw_fail = 0;
            foreach ($tw_prices as $tp) {
                if (!$tp) continue;
                $parts = explode(":", $tp);
                if (count($parts) == 2) {
                    $tw_price = round((float)$parts[1]);
                    // Для Timeweb тоже считаем через сумму компонентов
                    $tw_all_comps = $wpdb53->get_results(
                        "SELECT sp.component_id, sc.price, sp.quantity
                         FROM xsite_store_products sp
                         LEFT JOIN xsite_store_components sc ON sc.id = sp.component_id
                         WHERE sp.product_id = '" . (int)$parts[0] . "' AND sp.site = 'cvetyru-vn'"
                    );
                    if ($tw_all_comps) {
                        $tw_calc = 0;
                        foreach ($tw_all_comps as $tac) {
                            $tw_calc += (float)$tac->price * (int)$tac->quantity;
                        }
                        $tw_calc = round($tw_calc);
                        if (abs($tw_calc - $tw_price) <= 5) {
                            $tw_ok++;
                        } else {
                            $tw_fail++;
                            $timeweb_issues[] = "product_id:" . (int)$parts[0]
                                . " component_id:" . $component_id
                                . " expected~" . $tw_calc . " got:" . $tw_price;
                        }
                    }
                }
            }
            $tw_status = ($tw_fail == 0) ? "OK(" . $tw_ok . ")" : "FAIL(" . $tw_fail . "/" . count($tw_product_ids) . ")";
        } else {
            $tw_status = "no_ssh";
        }
    }

    $c_name = mb_substr($c->name, 0, 28);

    printf("%-6d %-30s %10.2f %12s %12s\n",
        $component_id,
        $c_name,
        $component_price,
        $beget_status,
        $tw_status
    );

    if (strpos($beget_status, "FAIL") !== false || strpos($tw_status, "FAIL") !== false) {
        $total_fail++;
    } else {
        $total_ok++;
    }
}

echo str_repeat("-", 75) . "\n";
echo "Итого: OK=" . $total_ok . " FAIL=" . $total_fail . "\n\n";

if (count($timeweb_issues) > 0) {
    echo "Проблемы на Timeweb:\n";
    foreach ($timeweb_issues as $issue) {
        echo "  - " . $issue . "\n";
    }
    echo "\n";
}

echo "Для ручной проверки на Timeweb выполни:\n";
echo "  SSHPASS='eJF6qQq@A*Jud1' sshpass -e ssh ... root@85.193.87.217 \\\n";
echo "  \"wp --path=/var/www/fastuser/data/www/cvetyru-vn.ru eval 'set_product_price(PRODUCT_ID); echo wc_get_price_to_display(wc_get_product(PRODUCT_ID));'\"\n\n";

echo "Лог файл Beget: ~/53flowers.com/public_html/wp-content/themes/xsiteshop/logs/xs_store.log\n";
echo "Лог файл Timeweb: /var/www/fastuser/data/www/cvetyru-vn.ru/wp-content/plugins/xs_store/cron/log\n\n";
