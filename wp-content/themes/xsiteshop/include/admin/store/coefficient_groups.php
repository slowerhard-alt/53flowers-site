<?php global $cg_list; ?>
<script> xs_active_menu("admin.php?page=store&section=coefficient_groups") </script>
<style>
.cg_table{width:1000px}
.cg_table th{background:#fff;white-space:nowrap}
.cg_table td{padding:6px 8px;vertical-align:middle}
.cg_row_name{font-weight:600;color:#2271b1;cursor:pointer;text-decoration:none}
.cg_row_name:hover{text-decoration:underline}
.cg_color_chip{display:inline-block;width:18px;height:18px;border-radius:3px;border:1px solid #ccc;vertical-align:middle}
.cg_tiers_cell{font-family:monospace;font-size:12px;color:#444}
.cg_tier_chunk{display:inline-block;padding:1px 6px;margin-right:4px;background:#f0f0f1;border-radius:2px}
.cg_components_count{color:#2271b1;font-weight:600}
.cg_components_count_zero{color:#999}
.cg_action_btn{cursor:pointer;color:#2271b1;margin-right:8px;text-decoration:underline}
.cg_del_btn{cursor:pointer;color:#c0392b;text-decoration:underline}
.cg_del_btn.cg_del_disabled{color:#ccc;cursor:not-allowed;text-decoration:none}
.cg_header_row{margin-bottom:12px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.cg_default_form{display:inline-flex;gap:6px;align-items:center;padding:6px 12px;background:#f8f9fa;border:1px solid #e0e0e0;border-radius:3px}
.cg_default_form label{font-size:12px;color:#555}
</style>

<div class="store_report_page">
    <div class="store_report_page__fix"><?
        xs_get_message();
    ?>
    <div class="cg_header_row">
        <div data-modal="coefficient-group-add" class="button button-primary">+ Создать группу</div>
        <span id="cg_recompute_all" class="button">Пересчитать все цены</span>
        <div class="cg_default_form">
            <form method="post" style="display:inline-flex;gap:6px;align-items:center;margin:0">
                <label>Дефолт-коэффициент:</label>
                <input type="number" name="default_coefficient" min="0" step="0.001"
                    value="<?=esc_attr((float)xs_get_option('xs_default_coefficient') ?: 2.5)?>"
                    style="width:70px" />
                <input type="hidden" name="save_default_coefficient" value="y" />
                <input type="submit" value="Сохранить" class="button" style="padding:0 8px;height:26px" />
                <span style="color:#888;font-size:11px">для компонентов без группы</span>
            </form>
        </div>
    </div>
    </div>

    <div class="store_table__container">
        <table class="wp-list-table widefat striped cg_table">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center">№</th>
                    <th>Название</th>
                    <th style="width:50px;text-align:center">Цвет</th>
                    <th>Диапазоны</th>
                    <th style="width:120px;text-align:center">Компоненты</th>
                    <th style="width:180px">Действия</th>
                </tr>
            </thead>
            <tbody>
<?php
if (!empty($cg_list)) {
    foreach ($cg_list as $g) {
        $color_display = $g->color ? 'background:' . esc_attr($g->color) . ';' : 'background:#fafafa;border-style:dashed;';
        $tiers_html = '';
        if (!empty($g->tiers)) {
            foreach ($g->tiers as $t) {
                $range = $t->max_days === null
                    ? (int)$t->min_days . '+'
                    : (int)$t->min_days . '–' . (int)$t->max_days;
                $k_str = rtrim(rtrim((string)$t->k, '0'), '.');
                $style = $t->color ? ' style="background:' . esc_attr($t->color) . ';color:#000"' : '';
                $tiers_html .= '<span class="cg_tier_chunk"' . $style . '>' . esc_html($range) . '·' . esc_html($k_str) . '</span>';
            }
        } else {
            $tiers_html = '<em style="color:#aaa">нет диапазонов</em>';
        }
        $cnt = (int)$g->components_count;
        $cnt_class = $cnt > 0 ? 'cg_components_count' : 'cg_components_count_zero';
        $del_class = $cnt > 0 ? 'cg_del_btn cg_del_disabled' : 'cg_del_btn';
        $del_title = $cnt > 0 ? 'Нельзя удалить: используется ' . $cnt . ' компонентами' : 'Удалить группу';
?>
                <tr data-group-id="<?=(int)$g->id?>">
                    <td style="text-align:center;color:#888"><?=(int)$g->sort?></td>
                    <td>
                        <a data-modal="coefficient-group-detail" data-id="<?=(int)$g->id?>" href="#" class="cg_row_name"><?=esc_html($g->name)?></a>
                    </td>
                    <td style="text-align:center">
                        <span class="cg_color_chip" style="<?=$color_display?>"></span>
                    </td>
                    <td class="cg_tiers_cell"><?=$tiers_html?></td>
                    <td style="text-align:center">
                        <span class="<?=$cnt_class?>"><?=$cnt?></span>
                    </td>
                    <td>
                        <span class="cg_action_btn" data-modal="coefficient-group-detail" data-id="<?=(int)$g->id?>">✎ Редактировать</span>
                        <span class="<?=$del_class?>" data-id="<?=(int)$g->id?>" title="<?=esc_attr($del_title)?>">🗑 Удалить</span>
                    </td>
                </tr>
<?php
    }
} else {
?>
                <tr><td colspan="6" style="padding:20px;text-align:center;color:#888">
                    Групп коэффициентов нет. Нажмите <b>"+ Создать группу"</b> чтобы добавить первую.
                </td></tr>
<?php
}
?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;padding:12px 16px;background:#f8f9fa;border-left:3px solid #2271b1;font-size:13px;color:#555;max-width:900px">
        <b>Как работают коэффициенты:</b><br>
        Цена компонента = <code>Себес × k</code>, где <code>k</code> берётся по количеству дней на складе.
        Приоритет: <b>П.цена</b> компонента → <b>коэффициент группы</b> → <b>дефолт-коэффициент</b>.
        Если компонент не привязан к группе коэф., используется дефолт.
    </div>
</div>

<div id="cg_recompute_progress" style="display:none;margin:12px 0;padding:12px 16px;background:#fff;border:1px solid #2271b1;border-radius:3px;max-width:700px"></div>

<script>
// Глобальный батч-раннер: пересчёт цен компонентов порциями.
// group_id=0 → все; >0 → только из группы.
// $progress — jQuery элемент-контейнер для прогресс-бара.
// cb(result) — колбек по завершении.
window.cgBatchRecompute = function(group_id, $progress, cb) {
    var AJAX = '/wp-content/themes/xsiteshop/load/admin/ajax.php';
    var BATCH = 50;

    $progress.show().html('<div style="color:#555">Получаю список компонентов...</div>');

    jQuery.post(AJAX, {action:'coefficient-group-recompute-list', group_id: group_id}, function(resp){
        if (!resp || resp.status !== 'good' || !resp.ids) {
            $progress.html('<p style="color:#c0392b;margin:0">Не удалось получить список компонентов</p>');
            if (cb) cb({ok:false});
            return;
        }
        var ids = resp.ids;
        var total = ids.length;
        if (total === 0) {
            $progress.html('<p style="margin:0">Нет компонентов для пересчёта</p>');
            if (cb) cb({ok:true, processed:0});
            return;
        }

        var processed = 0, errors = 0, startTime = Date.now();

        $progress.html(
            '<div><b>Пересчёт цен:</b> <span class="cg_pbar_text">0 / ' + total + ' (0%)</span></div>' +
            '<div style="margin-top:6px;background:#eee;border-radius:3px;overflow:hidden;height:18px;border:1px solid #ccc">' +
              '<div class="cg_pbar_fill" style="background:#2271b1;height:100%;width:0%;transition:width 0.3s ease"></div>' +
            '</div>' +
            '<div class="cg_pbar_eta" style="margin-top:4px;font-size:11px;color:#888">Подождите...</div>'
        );

        function updateBar() {
            var pct = total > 0 ? Math.round(processed / total * 100) : 0;
            $progress.find('.cg_pbar_text').text(processed + ' / ' + total + ' (' + pct + '%)');
            $progress.find('.cg_pbar_fill').css('width', pct + '%');
            var elapsed = (Date.now() - startTime) / 1000;
            if (processed > 0 && processed < total) {
                var eta = Math.round(elapsed / processed * (total - processed));
                $progress.find('.cg_pbar_eta').text('Осталось примерно ' + eta + ' сек (обработано за ' + Math.round(elapsed) + ' сек)');
            } else if (processed >= total) {
                $progress.find('.cg_pbar_eta').text('Всего ' + Math.round(elapsed) + ' сек');
            }
        }

        function runBatch() {
            if (processed >= total) {
                var totalSec = Math.round((Date.now() - startTime) / 1000);
                $progress.append(
                    '<p style="color:#27ae60;margin:8px 0 0"><b>Готово.</b> Пересчитано: ' + processed +
                    (errors > 0 ? ', ошибок: <span style="color:#c0392b">' + errors + '</span>' : '') +
                    '. Время: ' + totalSec + ' сек.</p>'
                );
                if (cb) cb({ok:true, processed:processed, errors:errors});
                return;
            }
            var batch = ids.slice(processed, processed + BATCH);
            jQuery.post(AJAX, {action:'coefficient-group-recompute-batch', ids: batch}, function(resp){
                if (resp && resp.status === 'good') {
                    processed += (resp.processed || batch.length);
                } else {
                    processed += batch.length;
                    errors += batch.length;
                }
                updateBar();
                setTimeout(runBatch, 50);
            }, 'json').fail(function(){
                processed += batch.length;
                errors += batch.length;
                updateBar();
                setTimeout(runBatch, 200);
            });
        }

        runBatch();
    }, 'json').fail(function(){
        $progress.html('<p style="color:#c0392b;margin:0">Не удалось получить список: ошибка сети</p>');
        if (cb) cb({ok:false});
    });
};

jQuery(document).ready(function($){

    $(document).on('click', '.cg_del_btn:not(.cg_del_disabled)', function(){
        var id = $(this).data('id');
        if (!confirm('Удалить группу коэффициентов? Это необратимо.')) return;
        $.post('/wp-content/themes/xsiteshop/load/admin/ajax.php',
            {action:'coefficient-group-del', id:id},
            function(resp){
                if (resp && resp.status === 'good') {
                    location.reload();
                } else {
                    alert((resp && resp.message) || 'Ошибка удаления');
                }
            }, 'json'
        ).fail(function(){ alert('Ошибка сети'); });
    });

    $(document).on('click', '.cg_del_btn.cg_del_disabled', function(){
        alert($(this).attr('title'));
    });

    $('#cg_recompute_all').on('click', function(){
        if (!confirm('Пересчитать цены для ВСЕХ компонентов?\n\nЭто обновит цены в БД и каскадом пересчитает стоимость WooCommerce-товаров. Процесс идёт пачками по 50 компонентов с видимым прогрессом — можешь спокойно следить. Не закрывай страницу до завершения.')) return;
        var $btn = $(this);
        $btn.prop('disabled', true).text('Пересчитываю...');
        window.cgBatchRecompute(0, $('#cg_recompute_progress'), function(res){
            $btn.prop('disabled', false).text('Пересчитать все цены');
        });
    });

});
</script>
