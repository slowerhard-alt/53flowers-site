<script> xs_active_menu("admin.php?page=store&section=group") </script>
<style>#screen-meta ~ .notice{display:none!important} .xs_data_table tbody tr td input{position:relative;z-index:2}
.store_discount_btns{display:inline-flex;gap:2px;flex-wrap:nowrap}
.store_discount_btn{cursor:pointer;padding:1px 5px;background:#f0f0f1;border:1px solid #c3c4c7;border-radius:2px;font-size:11px;line-height:18px;color:#1d2327}
.store_discount_btn:hover{background:#2271b1;border-color:#2271b1;color:#fff}
.store_sort_btns{display:inline-flex;flex-direction:column;gap:8px}
.store_sort_btn{cursor:pointer;font-size:11px;line-height:13px;padding:0 3px;color:#2271b1;border:none;background:none}
.store_sort_btn:hover{text-decoration:underline}

.xs_data_table tbody .row-no-price td { opacity:0.6; }
.xs_data_table tbody .row-no-price td:first-child,
.xs_data_table tbody .row-no-price td:nth-child(2) { opacity:1; }
.xs_data_table tbody .cell-loss input.store_input_forced_price { border-color:#e74c3c!important; background:#fff5f5; }
.xs_data_table thead tr { background:#fff !important; }
.xs_data_table thead th { background:#fff; white-space:nowrap; }

.store_hint_margin { font-size:10px; margin-left:3px; }
.store_hint_margin.margin-loss { color:#e74c3c; }
.store_hint_margin.margin-ok { color:#27ae60; }

.group-category-divider td { background:#f0f0f1; font-weight:600; font-size:12px; color:#555; padding:4px 8px; }
.xs_data_table tbody td { padding: 3px 6px; }

#store_batch_panel { display:none; background:#fff; border:1px solid #ccd0d4; padding:8px 12px; margin-bottom:8px; border-radius:3px; align-items:center; gap:8px; }

.store_input_note { width:120px; font-size:11px; color:#666; }

.sortable-col { cursor:pointer; user-select:none; }
.sortable-col:hover { color:#2271b1; }
.sortable-col.sorted-asc:after { content:" ↑"; }
.sortable-col.sorted-desc:after { content:" ↓"; }

.price-log-badge { font-size:10px; color:#888; cursor:pointer; text-decoration:underline; }

.xs_data_table { table-layout:fixed; width:1005px; }

.store_group_cnt { display:inline-block; min-width:24px; text-align:right; margin-right:4px; }
.xs_data_table td:nth-child(4),
.xs_data_table td:nth-child(5) { text-align:center; }
.xs_data_table th:nth-child(5),
.xs_data_table td:nth-child(5) { min-width:70px; }
</style>
<div class="store_report_page">
    <div class="store_report_page__fix"><?

        xs_get_message();

        ?><form class="xs_filter" id="filter" method="get" action="" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:10px">
            <input type="hidden" name="page" value="<?=esc_attr($_GET['page'])?>" />
            <input type="hidden" name="section" value="<?=esc_attr($_GET['section'])?>" />

            <span style="position:relative;display:inline-flex;align-items:center;gap:4px">
                <span id="store_search_toggle" style="cursor:pointer;display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border:1px solid #c3c4c7;border-radius:3px;background:#f0f0f1;font-size:14px" title="Поиск">🔍</span>
                <input type="text" id="store_search_input" name="filter[search]"
                    value="<?=esc_attr(isset($xs_filter['search']) ? $xs_filter['search'] : '') ?>"
                    placeholder="Поиск..."
                    style="width:160px;display:<?=!empty($xs_filter['search']) ? 'inline-block' : 'none'?>" />
                <input type="submit" value="→" class="button"
                    id="store_search_submit"
                    style="display:<?=!empty($xs_filter['search']) ? 'inline-block' : 'none'?>;padding:0 6px" />
                <?php if (!empty($xs_filter['search'])): ?>
                    <a href="/wp-admin/admin.php?page=<?=esc_attr($_GET['page'])?>&section=<?=esc_attr($_GET['section'])?>">×</a>
                <?php endif; ?>
            </span>

            <?php
            $btn_style_base = 'cursor:pointer;padding:2px 10px;border-radius:3px;font-size:12px;line-height:22px;border:1px solid #2271b1;';
            $btn_active = $btn_style_base . 'background:#2271b1;color:#fff;';
            $btn_inactive = $btn_style_base . 'background:#fff;color:#2271b1;';
            ?>
            <span class="store_filter_btn" data-name="in_stock" style="<?=!empty($xs_filter['in_stock']) ? $btn_active : $btn_inactive?>">С остатком</span>
            <span class="store_filter_btn" data-name="no_price" style="<?=!empty($xs_filter['no_price']) ? $btn_active : $btn_inactive?>">Без П.цены</span>
            <span class="store_filter_btn" data-name="is_loss"  style="<?=!empty($xs_filter['is_loss'])  ? $btn_active : $btn_inactive?>">Убыточные</span>

            <input type="hidden" name="filter[in_stock]" id="hid_in_stock" value="<?=!empty($xs_filter['in_stock']) ? 'y' : ''?>" />
            <input type="hidden" name="filter[no_price]" id="hid_no_price" value="<?=!empty($xs_filter['no_price']) ? 'y' : ''?>" />
            <input type="hidden" name="filter[is_loss]"  id="hid_is_loss"  value="<?=!empty($xs_filter['is_loss'])  ? 'y' : ''?>" />

            <?php if ($setFilter && empty($xs_filter['search'])): ?>
                <a href="/wp-admin/admin.php?page=<?=esc_attr($_GET['page'])?>&section=<?=esc_attr($_GET['section'])?>"
                   style="cursor:pointer;padding:2px 8px;border-radius:3px;font-size:12px;line-height:22px;border:1px solid #999;background:#fff;color:#666;text-decoration:none">× Сбросить</a>
            <?php endif; ?>

            &nbsp;
            <div data-modal="group-add" class="button">Создать группу</div>
            <div id="store_group_auto_assign" class="button">Автопривязка по названию</div>
            <a href="/moysklad/update_all.php?is_admin=y" target="_blank" class="button">Обновить цены с МС</a>
            <span id="store_fill_x2_all" class="button">Подставить x2 всем</span>
            <?php if (isset($xs_priced_groups)): ?>
                <span style="color:#555;font-size:12px">С П.ценой: <b><?=(int)$xs_priced_groups?></b> из <b><?=(int)$xs_total_groups?></b></span>
            <?php endif; ?>
        </form>
    </div>

    <div id="store_auto_assign_result" style="display:none; margin:0 0 12px; padding:14px 16px; background:#fff; border:1px solid #ccd0d4; border-radius:3px; max-height:60vh; overflow-y:auto;"></div>

    <?php
    $xs_ms_markup = (int)get_option('xs_ms_price_markup', 0);
    ?>
    <form method="post" style="display:inline-flex;align-items:center;gap:6px;margin-bottom:8px">
        <label style="font-size:12px;color:#555">Наценка в МойСклад:</label>
        <input type="number" name="ms_markup" value="<?=(int)$xs_ms_markup?>" min="0" max="200" step="1" style="width:60px" placeholder="0" />
        <span style="font-size:12px;color:#777">% от П.цены, до 5₽</span>
        <input type="hidden" name="save_ms_markup" value="y" />
        <input type="submit" value="Сохранить" class="button" style="padding:0 8px;height:26px" />
        <?php if ($xs_ms_markup > 0): ?>
            <span style="color:#27ae60;font-size:12px">✓ Активна</span>
        <?php endif; ?>
    </form>

    <?php if (isset($xs_unassigned_count) && $xs_unassigned_count > 0): ?>
    <div style="margin-bottom:8px;padding:6px 12px;background:#fff3cd;border:1px solid #ffc107;border-radius:3px;font-size:13px">
        ⚠️ <b><?=(int)$xs_unassigned_count?></b> цветов без группы
    </div>
    <?php endif; ?>
    <form action="" method="post">
        <div id="store_batch_panel">
            <b id="store_batch_count">0</b> групп выбрано. &nbsp;
            Скидка: <input type="number" id="store_batch_discount" min="0" max="100" style="width:60px" placeholder="%">
            &nbsp;<span class="button" id="store_batch_apply">Применить</span>
            &nbsp;<span class="button" id="store_batch_cancel">Отмена</span>
        </div>
        <div class="store_table__container">
            <table class="wp-list-table store_table widefat striped xs_data_table">
                <thead>
                    <tr>
                        <th style="width:45px">
                            <input type="checkbox" id="store_batch_check_all" title="Выбрать все">
                        </th>
                        <th>Название</th>
                        <th class="sortable-col" data-col="days" style="width:32px;text-align:center">Дни</th>
                        <th class="sortable-col" data-col="qty" style="width:58px;text-align:center">Остаток</th>
                        <th class="sortable-col" data-col="cost" style="width:42px;text-align:center">Себес</th>
                        <th style="width:115px">П.цена / Маржа</th>
                        <th style="width:200px">% скидки</th>
                    </tr>
                </thead>
                <tbody><?

                if ($xs_data && count($xs_data)) {
                    foreach ($xs_data as $v) {

                        $days_bg = '';
                        $days_val = (float)$v->avg_days;
                        if     ($days_val <= 1)  $days_bg = '#a5d6a7';
                        elseif ($days_val <= 3)  $days_bg = '#c8e6c9';
                        elseif ($days_val <= 7)  $days_bg = '#fff9c4';
                        elseif ($days_val <= 14) $days_bg = '#ffcc80';
                        else                      $days_bg = '#ef9a9a';

                        $qty_class = (int)$v->avg_quantity > 0 ? 'cell--stock-ok' : 'cell--stock-zero';

                        $fp_value = ($v->min_fp == $v->max_fp && $v->min_fp > 0) ? $v->min_fp : '';

                        $sale_percent_val = isset($v->sample_sale_rules['percent']) && $v->sample_sale_rules['percent'] > 0
                            ? (int)$v->sample_sale_rules['percent']
                            : '';

                        $row_class = ($fp_value == '') ? 'row-no-price' : '';

                        $x2_val = $v->avg_original_price > 0 ? ceil($v->avg_original_price * 2 / 5) * 5 : 0;
                        $cell_class = ($fp_value > 0 && $v->avg_original_price > 0 && $fp_value < $v->avg_original_price) ? 'cell-loss' : '';
                        $margin_html = '';
                        if ($fp_value > 0 && $v->avg_original_price > 0) {
                            $margin = round(($fp_value / $v->avg_original_price - 1) * 100);
                            $margin_class = $margin < 0 ? 'margin-loss' : 'margin-ok';
                            $margin_html = '<span class="store_hint_margin ' . $margin_class . '">' . ($margin > 0 ? '+' : '') . $margin . '%</span>';
                        }
                        $price_log_html = '';
                        if (!empty($v->price_change_count) && $v->price_change_count > 0) {
                            $last = date('d.m', strtotime($v->last_price_change));
                            $price_log_html = ' <span class="price-log-badge" data-id="' . (int)$v->id . '" title="История цен" style="cursor:pointer;font-size:14px;opacity:0.6">🕐</span>';
                        }

                        ?><tr class="<?=esc_attr($row_class)?>">
                            <td style="white-space:nowrap;vertical-align:middle;text-align:center">
                                <input type="checkbox" class="store_batch_cb" data-id="<?=(int)$v->id ?>">
                                <span class="store_sort_btns">
                                    <span class="store_sort_btn store_sort_up" data-id="<?=(int)$v->id ?>" title="Переместить вверх">↑</span>
                                    <span class="store_sort_btn store_sort_down" data-id="<?=(int)$v->id ?>" title="Переместить вниз">↓</span>
                                </span>
                            </td>
                            <td>
                                <span class="store_group_cnt" data-id="<?=(int)$v->id ?>" style="color:#2271b1;cursor:pointer;font-size:11px;margin-right:4px;text-decoration:underline"><?=(int)$v->cnt?></span>
                                <a data-modal="group-detail" data-id="<?=(int)$v->id ?>" href="#"><?=esc_html($v->name) ?></a>
                            </td>
                            <td style="background:<?=esc_attr($days_bg) ?>"><?=$days_val > 0 ? $days_val : '—' ?></td>
                            <td class="<?=esc_attr($qty_class) ?>"><?=$v->avg_quantity > 0 ? (int)$v->avg_quantity : '—' ?></td>
                            <td style="text-align:center"><?=$v->avg_original_price > 0 ? number_format((int)$v->avg_original_price, 0, ',', ' ') : '—' ?><?php if (!empty($v->avg_cost_prev_week) && $v->avg_cost_prev_week > 0): ?><?php if ($v->avg_original_price > $v->avg_cost_prev_week): ?><span style="color:#c0392b;font-size:10px;">↑</span><?php elseif ($v->avg_original_price < $v->avg_cost_prev_week): ?><span style="color:#27ae60;font-size:10px;">↓</span><?php endif; ?><?php endif; ?></td>
                            <td class="<?=esc_attr($cell_class)?>">
                                <div>
                                    <?php if ($x2_val > 0): ?>
                                        <span class="store_hint_x2 store_hint_x2--clickable" title="Вставить в П.цену" style="color:#999;font-size:11px;cursor:pointer;margin-right:4px"><?=number_format($x2_val, 0, ',', ' ')?></span>
                                    <?php endif; ?>
                                    <input type="number" name="g[<?=(int)$v->id ?>][forced_price]"
                                        class="store_input_forced_price"
                                        value="<?=esc_attr($fp_value) ?>"
                                        data-x2="<?=(int)$x2_val ?>"
                                        min="0" step="1" style="width:75px"
                                        placeholder="—" />
                                </div>
                                <div style="margin-top:2px;font-size:11px"><?=$margin_html?><?=$price_log_html?><?php
                                $note_val = isset($v->note) ? $v->note : '';
                                $note_icon = $note_val ? '✏️' : '＋';
                                $note_title = $note_val ? esc_attr($note_val) : 'Добавить заметку';
                                ?><span class="store_note_btn" data-id="<?=(int)$v->id?>" data-note="<?=esc_attr($note_val)?>" title="<?=$note_title?>" style="cursor:pointer;color:#aaa;margin-left:4px;font-size:11px"><?=$note_icon?></span></div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:flex-start;gap:4px">
                                    <input type="number" name="g[<?=(int)$v->id ?>][sale_percent]"
                                        class="store_input_sale_percent"
                                        value="<?=esc_attr($sale_percent_val) ?>"
                                        min="0" max="100" step="1" style="width:50px"
                                        placeholder="—" />
                                    <div style="display:grid;grid-template-columns:repeat(3,auto);gap:2px">
                                        <span class="store_discount_btn" data-val="5">5</span>
                                        <span class="store_discount_btn" data-val="10">10</span>
                                        <span class="store_discount_btn" data-val="15">15</span>
                                        <span class="store_discount_btn" data-val="20">20</span>
                                        <span class="store_discount_btn" data-val="25">25</span>
                                        <span class="store_discount_btn" data-val="30">30</span>
                                    </div>
                                    <span class="store_cell_clear" title="Очистить" style="cursor:pointer;color:#999;margin-left:3px">×</span>
                                </div>
                            </td>
                        </tr><?
                    }
                } else {
                    ?><tr><td colspan="7">Группы не найдены.</td></tr><?
                }

                ?></tbody>
            </table>
        </div>

        <div class="store_table_edit">
            <input type="submit" value="Сохранить" class="button-primary">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label>
                <input type="checkbox" value="y" name="is_send_bitrix"> Чат Б24
            </label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label>
                <input type="checkbox" value="y" name="is_create_task"> Задача Б24
            </label>
        </div>
        <input type="hidden" value="y" name="edit_groups" />
    </form>
</div>
<script>
jQuery(document).on('keydown', '.store_input_forced_price', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        var $next = jQuery(this).closest('tr').next('tr').find('.store_input_forced_price');
        if ($next.length) $next.focus().select();
    }
});
jQuery(document).on('click', '.store_cell_clear', function() {
    jQuery(this).closest('td').find('input').val('');
});
</script>

<script>
jQuery(document).ready(function() {
    jQuery('#store_group_auto_assign').on('click', function() {
        var $btn = jQuery(this);
        var $result = jQuery('#store_auto_assign_result');
        $btn.addClass('disabled').text('Анализирую...');
        $result.hide();

        jQuery.post('/wp-content/themes/xsiteshop/load/admin/ajax.php',
            {action: 'group-auto-assign', dry_run: 'y'},
            function(resp) {
                $btn.removeClass('disabled').text('Автопривязка по названию');
                if(resp.status === 'preview') {
                    if(resp.total === 0) {
                        $result.html('<p>Новых компонентов для привязки не найдено.</p>').show();
                        return;
                    }
                    var html = '<strong>Будет привязано ' + resp.total + ' компонентов:</strong>';
                    html += '<table class="wp-list-table widefat striped" style="margin:10px 0">';
                    html += '<thead><tr><th>Группа</th><th>Компоненты</th></tr></thead><tbody>';
                    jQuery.each(resp.groups, function(i, g) {
                        html += '<tr><td style="white-space:nowrap;vertical-align:top"><b>' + g.group + '</b><br><span style="color:#888">(' + g.count + ' шт.)</span></td>';
                        html += '<td style="font-size:12px">' + g.names.join(', ');
                        if(g.count > g.names.length) html += ' <em style="color:#888">и ещё ' + (g.count - g.names.length) + '...</em>';
                        html += '</td></tr>';
                    });
                    html += '</tbody></table>';
                    html += '<button id="store_auto_assign_execute" class="button-primary">Выполнить привязку</button>';
                    html += ' <button id="store_auto_assign_cancel" class="button">Закрыть</button>';
                    $result.html(html).show();

                    jQuery(document).on('click', '#store_auto_assign_execute', function() {
                        var $ex = jQuery(this);
                        $ex.addClass('disabled').text('Выполняю...');
                        jQuery.post('/wp-content/themes/xsiteshop/load/admin/ajax.php',
                            {action: 'group-auto-assign', dry_run: 'n'},
                            function(r) {
                                alert(r.message);
                                location.reload();
                            }, 'json'
                        );
                    });
                    jQuery(document).on('click', '#store_auto_assign_cancel', function() {
                        $result.hide();
                    });
                } else {
                    $result.html('<p style="color:red">Ошибка запроса</p>').show();
                }
            }, 'json'
        ).fail(function() {
            $btn.removeClass('disabled').text('Автопривязка по названию');
            $result.html('<p style="color:red">Ошибка запроса</p>').show();
        });
    });

    jQuery(document).on('click', '.store_sort_up, .store_sort_down', function() {
        var $btn = jQuery(this);
        var group_id = $btn.data('id');
        var direction = $btn.hasClass('store_sort_up') ? 'up' : 'down';
        $btn.css('opacity', '0.4');
        jQuery.post('/wp-content/themes/xsiteshop/load/admin/ajax.php',
            {action: 'group-sort-swap', group_id: group_id, direction: direction},
            function(resp) {
                if (resp && resp.status === 'ok') {
                    location.reload();
                } else {
                    $btn.css('opacity', '1');
                    alert('Ошибка сортировки');
                }
            }, 'json'
        ).fail(function() {
            $btn.css('opacity', '1');
        });
    });
});
</script>

<script>
jQuery(document).ready(function($) {

    $('.sortable-col').on('click', function() {
        var idx = $(this).index();
        var asc = !$(this).hasClass('sorted-asc');
        $('.sortable-col').removeClass('sorted-asc sorted-desc');
        $(this).addClass(asc ? 'sorted-asc' : 'sorted-desc');
        var rows = $('.xs_data_table tbody tr:not(.group-category-divider):not(.group-components-row)').toArray();
        rows.sort(function(a, b) {
            var va = parseFloat($(a).find('td').eq(idx).text().replace(/[^\d.-]/g,'')) || 0;
            var vb = parseFloat($(b).find('td').eq(idx).text().replace(/[^\d.-]/g,'')) || 0;
            return asc ? va - vb : vb - va;
        });
        $.each(rows, function(i, row) { $('.xs_data_table tbody').append(row); });
    });

    $('#store_fill_x2_all').on('click', function() {
        $('.store_input_forced_price').each(function() {
            if (!$(this).val()) {
                var x2 = parseInt($(this).data('x2'));
                if (x2 > 0) $(this).val(x2);
            }
        });
    });

    $('#store_batch_check_all').on('change', function() {
        $('.store_batch_cb').prop('checked', this.checked);
        updateBatchPanel();
    });
    $(document).on('change', '.store_batch_cb', function() {
        updateBatchPanel();
    });
    function updateBatchPanel() {
        var cnt = $('.store_batch_cb:checked').length;
        if (cnt > 0) {
            $('#store_batch_count').text(cnt);
            $('#store_batch_panel').css('display', 'flex');
        } else {
            $('#store_batch_panel').hide();
        }
    }
    $('#store_batch_apply').on('click', function() {
        var pct = parseInt($('#store_batch_discount').val());
        if (isNaN(pct) || pct < 0 || pct > 100) return;
        $('.store_batch_cb:checked').each(function() {
            $(this).closest('tr').find('.store_input_sale_percent').val(pct);
        });
        $('.store_batch_cb').prop('checked', false);
        $('#store_batch_check_all').prop('checked', false);
        $('#store_batch_panel').hide();
        $('#store_batch_discount').val('');
    });
    $('#store_batch_cancel').on('click', function() {
        $('.store_batch_cb').prop('checked', false);
        $('#store_batch_check_all').prop('checked', false);
        $('#store_batch_panel').hide();
    });

    $(document).on('click', '.store_group_cnt', function() {
        var $this = $(this);
        var group_id = $this.data('id');
        var $row = $this.closest('tr');
        var $next = $row.next('.group-components-row');
        if ($next.length) { $next.toggle(); return; }
        var origText = $this.text();
        $this.text('...');
        $.post('/wp-content/themes/xsiteshop/load/admin/ajax.php',
            {action: 'group-get-components', group_id: group_id},
            function(resp) {
                $this.text(origText);
                if (resp.status === 'ok') {
                    var html = '<tr class="group-components-row"><td colspan="7" style="background:#f9f9f9;padding:8px 16px;font-size:12px">';
                    html += resp.names.join(' &middot; ');
                    if (resp.more > 0) html += ' <em style="color:#888">и ещё ' + resp.more + '</em>';
                    html += '</td></tr>';
                    $row.after(html);
                }
            }, 'json'
        ).fail(function() { $this.text(origText); });
    });

    $(document).on('dblclick', '[data-modal="group-detail"]', function(e) {
        e.preventDefault();
        var $a = $(this);
        var group_id = $a.data('id');
        var old_name = $a.text();
        var $input = $('<input type="text" style="width:200px">').val(old_name);
        $a.replaceWith($input);
        $input.focus().select();
        function save() {
            var new_name = $.trim($input.val());
            if (!new_name || new_name === old_name) {
                $input.replaceWith($a);
                return;
            }
            $.post('/wp-content/themes/xsiteshop/load/admin/ajax.php',
                {action: 'group-rename', group_id: group_id, name: new_name},
                function(resp) {
                    if (resp.status === 'ok') {
                        $a.text(new_name);
                    }
                    $input.replaceWith($a);
                }, 'json'
            ).fail(function() { $input.replaceWith($a); });
        }
        $input.on('blur', save).on('keydown', function(e) {
            if (e.which === 13) { e.preventDefault(); save(); }
            if (e.which === 27) { $input.replaceWith($a); }
        });
    });

    $(document).on('click', '.price-log-badge', function() {
        var group_id = $(this).data('id');
        var group_name = $(this).closest('tr').find('a[data-modal="group-detail"]').text();
        $.post('/wp-content/themes/xsiteshop/load/admin/ajax.php',
            {action: 'group-price-log', group_id: group_id},
            function(resp) {
                var overlay = $('<div>').css({position:'fixed',top:0,left:0,width:'100%',height:'100%',background:'rgba(0,0,0,0.4)',zIndex:99998});
                var popup = $('<div>').css({position:'fixed',top:'50%',left:'50%',transform:'translate(-50%,-50%)',background:'#fff',borderRadius:'6px',boxShadow:'0 8px 32px rgba(0,0,0,0.22)',padding:'20px 24px',minWidth:'420px',maxWidth:'600px',zIndex:99999,maxHeight:'80vh',overflowY:'auto'});
                var closeBtn = $('<span>').text('×').css({position:'absolute',top:'10px',right:'14px',cursor:'pointer',fontSize:'20px',color:'#888',lineHeight:'1'});
                var title = $('<div>').html('<b>История цен: ' + $('<div>').text(group_name).html() + '</b>').css({marginBottom:'12px',fontSize:'14px'});
                popup.append(closeBtn).append(title);
                if (resp.status === 'ok' && resp.rows && resp.rows.length) {
                    var table = $('<table>').css({width:'100%',borderCollapse:'collapse',fontSize:'13px'});
                    var thead = $('<thead>').append($('<tr>').append(
                        $('<th>').text('Дата').css({padding:'4px 8px',borderBottom:'1px solid #e0e0e0',textAlign:'left',fontWeight:600}),
                        $('<th>').text('Старая цена').css({padding:'4px 8px',borderBottom:'1px solid #e0e0e0',textAlign:'right',fontWeight:600}),
                        $('<th>').text('Новая цена').css({padding:'4px 8px',borderBottom:'1px solid #e0e0e0',textAlign:'right',fontWeight:600}),
                        $('<th>').text('Кто изменил').css({padding:'4px 8px',borderBottom:'1px solid #e0e0e0',textAlign:'left',fontWeight:600})
                    ));
                    table.append(thead);
                    var tbody = $('<tbody>');
                    $.each(resp.rows, function(i, r) {
                        var tr = $('<tr>').css({background: i % 2 === 0 ? '#fff' : '#f9f9f9'});
                        tr.append(
                            $('<td>').text(r.changed_at).css({padding:'4px 8px',borderBottom:'1px solid #f0f0f0'}),
                            $('<td>').text(r.old_price + ' ₽').css({padding:'4px 8px',borderBottom:'1px solid #f0f0f0',textAlign:'right',color:'#c0392b'}),
                            $('<td>').text(r.new_price + ' ₽').css({padding:'4px 8px',borderBottom:'1px solid #f0f0f0',textAlign:'right',color:'#27ae60'}),
                            $('<td>').text(r.changed_by).css({padding:'4px 8px',borderBottom:'1px solid #f0f0f0',color:'#555'})
                        );
                        tbody.append(tr);
                    });
                    table.append(tbody);
                    popup.append(table);
                } else {
                    popup.append($('<p>').text('История пуста').css({color:'#888'}));
                }
                $('body').append(overlay).append(popup);
                function closePop() { overlay.remove(); popup.remove(); }
                closeBtn.on('click', closePop);
                overlay.on('click', closePop);
            }, 'json'
        );
    });

    var $notePopover = $('#store_note_popover');
    var _noteGroupId = 0;

    $(document).on('click', '.store_note_btn', function(e) {
        _noteGroupId = $(this).data('id');
        $('#store_note_input').val($(this).data('note'));
        $notePopover.css({top: e.pageY - 10, left: e.pageX + 10}).show();
        $('#store_note_input').focus();
    });
    $('#store_note_close').on('click', function() { $notePopover.hide(); });
    $('#store_note_save').on('click', function() {
        var note = $('#store_note_input').val();
        $.post('/wp-content/themes/xsiteshop/load/admin/ajax.php',
            {action: 'group-save-note', group_id: _noteGroupId, note: note},
            function(resp) {
                if (resp.status === 'ok') {
                    var $btn = $('.store_note_btn[data-id="' + _noteGroupId + '"]');
                    $btn.data('note', note).attr('title', note || 'Добавить заметку').text(note ? '✏️' : '+');
                    $notePopover.hide();
                }
            }, 'json'
        );
    });
    $(document).on('keydown', '#store_note_input', function(e) {
        if (e.which === 13) $('#store_note_save').click();
        if (e.which === 27) $notePopover.hide();
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#store_note_popover, .store_note_btn').length) $notePopover.hide();
    });

    $('#store_search_toggle').on('click', function() {
        var $inp = $('#store_search_input');
        var $btn = $('#store_search_submit');
        var visible = $inp.is(':visible');
        $inp.toggle(!visible);
        $btn.toggle(!visible);
        if (!visible) $inp.focus();
    });

    $(document).on('click', '.store_filter_btn', function() {
        var name = $(this).data('name');
        var $hid = $('#hid_' + name);
        var isActive = $hid.val() === 'y';
        $('.store_filter_btn').each(function() {
            $('#hid_' + $(this).data('name')).val('');
        });
        if (!isActive) $hid.val('y');
        $('#filter').submit();
    });

});
</script>

<div id="store_note_popover" style="display:none;position:fixed;z-index:9999;background:#fff;border:1px solid #ccd0d4;border-radius:4px;padding:10px;box-shadow:0 2px 8px rgba(0,0,0,.15);min-width:220px">
    <input type="text" id="store_note_input" placeholder="Заметка..." style="width:180px;margin-right:6px">
    <button class="button-primary" id="store_note_save">Сохранить</button>
    <span id="store_note_close" style="cursor:pointer;color:#999;margin-left:6px">×</span>
</div>
