<script> xs_active_menu("admin.php?page=store&section=check") </script>
<style>
#screen-meta ~ .notice{display:none!important}
.xs_check_table { width:100%; border-collapse:collapse; margin-bottom:20px; }
.xs_check_table th { background:#f0f0f1; padding:6px 10px; text-align:left; font-size:12px; border-bottom:2px solid #c3c4c7; }
.xs_check_table td { padding:5px 10px; font-size:12px; border-bottom:1px solid #f0f0f1; vertical-align:top; }
.xs_check_product_row td { background:#fff; font-weight:600; font-size:13px; }
.xs_check_comp_row td { background:#fafafa; color:#555; }
.xs_check_diff_ok { color:#27ae60; }
.xs_check_diff_warn { color:#c0392b; font-weight:600; }
.xs_check_section_title { font-size:14px; font-weight:600; margin:0 0 8px; color:#1d2327; }
.xs_check_card { background:#fff; border:1px solid #c3c4c7; border-radius:3px; margin-bottom:16px; padding:12px 16px; }
.xs_check_reload { margin-bottom:12px; }
</style>
<div class="store_report_page">
    <div style="margin-bottom:12px;display:flex;align-items:center;gap:10px">
        <h2 style="margin:0;font-size:16px">Проверка цен</h2>
        <a href="?page=store&section=check" class="button">Перемешать</a>
        <span style="color:#888;font-size:12px">10 случайных товаров. Расчёт: ceil(SUM(цена×кол-во) / 5) × 5</span>
    </div>

    <?php if (empty($xs_check_data)): ?>
        <p>Нет товаров с компонентами.</p>
    <?php else: ?>

    <?php foreach ($xs_check_data as $item): ?>
    <div class="xs_check_card">
        <div style="display:flex;align-items:baseline;gap:12px;margin-bottom:8px">
            <span class="xs_check_section_title">
                <a href="/wp-admin/post.php?post=<?=(int)$item['product_id']?>&action=edit" target="_blank"><?=esc_html($item['product_name'])?></a>
            </span>
            <span style="font-size:12px;color:#555">
                Факт: <b><?=number_format($item['actual_price'], 0, ',', ' ')?> ₽</b>
                &nbsp;|&nbsp;
                Расчёт: <b><?=$item['calc_price'] > 0 ? number_format($item['calc_price'], 0, ',', ' ') . ' ₽' : '—'?></b>
                &nbsp;|&nbsp;
                <?php if ($item['calc_price'] > 0): ?>
                    Разница:
                    <b class="<?=$item['diff'] > 5 ? 'xs_check_diff_warn' : 'xs_check_diff_ok'?>">
                        <?=$item['diff'] > 0 ? number_format($item['diff'], 0, ',', ' ') . ' ₽' : '0 ₽'?>
                    </b>
                <?php endif; ?>
            </span>
        </div>

        <?php if (!empty($item['components'])): ?>
        <table class="xs_check_table">
            <thead>
                <tr>
                    <th>Компонент</th>
                    <th style="text-align:right;width:70px">Кол-во</th>
                    <th style="text-align:right;width:80px">П.цена</th>
                    <th style="text-align:right;width:80px">Себес</th>
                    <th style="text-align:right;width:90px">Сумма</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($item['components'] as $comp): ?>
                <?php
                $use_price = $comp->forced_price > 0 ? $comp->forced_price : $comp->price;
                $line_sum = $use_price * $comp->quantity;
                ?>
                <tr class="xs_check_comp_row">
                    <td>
                        <?=esc_html($comp->name)?>
                        <?php if ($comp->forced_price > 0): ?>
                            <span style="color:#2271b1;font-size:10px">(П.цена)</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:right"><?=number_format((float)$comp->quantity, 1, ',', ' ')?></td>
                    <td style="text-align:right"><?=$comp->forced_price > 0 ? number_format($comp->forced_price, 0, ',', ' ') . ' ₽' : '—'?></td>
                    <td style="text-align:right"><?=$comp->original_price > 0 ? number_format($comp->original_price, 0, ',', ' ') . ' ₽' : '—'?></td>
                    <td style="text-align:right"><?=number_format($line_sum, 0, ',', ' ')?> ₽</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="color:#888;font-size:12px">Компоненты не найдены.</p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>
</div>
