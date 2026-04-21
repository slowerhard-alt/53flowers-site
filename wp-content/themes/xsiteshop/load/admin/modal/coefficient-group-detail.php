<?defined("IS_CHECK") || exit;

if(!($row = get_coefficient_group(isset($get_data['id']) ? (int)$get_data['id'] : 0, true)))
{
	echo "Группа коэффициентов не найдена.";
	die();
}

?><div class="admin_modal__title">Группа коэффициентов «<?=esc_html($row->name) ?>»</div><?

xs_get_message();

?>

<form class="xs_ajax_form" action="#" data-action="coefficient-group-edit" data-datatype="json" method="post"><?

	xs_input('name', $row->name, 'Название', ['required' => true]);
	xs_input('color', $row->color ? $row->color : '#ffffff', 'Цвет для подсветки', ['type' => 'color']);
	xs_input('sort', $row->sort, 'Сортировка', ['type' => 'number', 'min' => 0, 'step' => 1]);
	xs_input('id', $row->id, '', ['type' => 'hidden']);

	?><br>
	<div class="xs_form_result"></div>
	<div class="admin_modal__buttons">
		<input class="button-primary" type="submit" value="Сохранить параметры">
		<?php if ((int)$row->components_count === 0): ?>
			<span class="admin_modal__delete">Удалить группу</span>
		<?php else: ?>
			<span style="color:#999;font-size:11px;margin-left:10px">Используется <?=(int)$row->components_count?> компонентами — нельзя удалить</span>
		<?php endif; ?>
	</div>
</form>

<br>
<div class="admin_modal__subtitle">Диапазоны коэффициентов (от-до дней → k)</div>

<form id="cg_tiers_form" action="#" method="post" style="margin-top:10px">
	<input type="hidden" name="post_data[group_id]" value="<?=(int)$row->id ?>">
	<table class="wp-list-table widefat striped" style="margin-bottom:8px">
		<thead>
			<tr>
				<th style="width:40px">№</th>
				<th style="width:80px;text-align:center">от (дн)</th>
				<th style="width:80px;text-align:center">до (дн)</th>
				<th style="width:100px;text-align:center">коэф. (k)</th>
				<th style="width:80px;text-align:center">цвет</th>
				<th style="width:40px"></th>
			</tr>
		</thead>
		<tbody id="cg_tiers_rows">
<?php
$i = 0;
if (!empty($row->tiers)) {
	foreach ($row->tiers as $t) {
		$max_val = $t->max_days === null ? '' : (int)$t->max_days;
		?>
			<tr class="cg_tier_row">
				<td style="text-align:center;color:#888"><?=$i + 1?></td>
				<td><input type="number" name="post_data[tiers][<?=$i?>][min_days]" value="<?=(int)$t->min_days?>" min="1" step="1" style="width:60px" required></td>
				<td><input type="number" name="post_data[tiers][<?=$i?>][max_days]" value="<?=esc_attr($max_val)?>" min="1" step="1" style="width:60px" placeholder="∞"></td>
				<td><input type="number" name="post_data[tiers][<?=$i?>][k]" value="<?=esc_attr(rtrim(rtrim((string)$t->k, '0'), '.'))?>" min="0.001" step="0.001" style="width:80px" required></td>
				<td style="text-align:center"><input type="color" name="post_data[tiers][<?=$i?>][color]" value="<?=esc_attr($t->color ? $t->color : '#ffffff')?>" style="width:40px;height:26px"></td>
				<td style="text-align:center"><span class="cg_tier_del" style="cursor:pointer;color:#c0392b;font-size:18px" title="Удалить диапазон">×</span></td>
			</tr>
		<?php
		$i++;
	}
}
?>
		</tbody>
	</table>
	<div style="margin-bottom:10px">
		<span class="button" id="cg_tier_add">+ Добавить диапазон</span>
		<span style="color:#888;font-size:11px;margin-left:10px">Пустое поле «до» = «∞, и дальше»</span>
	</div>
	<div class="xs_form_result" id="cg_tiers_result"></div>
	<div style="margin-top:6px">
		<input type="button" id="cg_tiers_save" class="button-primary" value="Сохранить диапазоны">
		<input type="button" id="cg_recompute_group" class="button" value="Пересчитать цены группы" style="margin-left:8px">
	</div>
</form>

<br>
<div class="admin_modal__subtitle">Компоненты группы (<?=(int)$row->components_count?>)</div>
<?php
$components = [];
if ($row->components_count > 0) {
	$components = $wpdb->get_results($wpdb->prepare(
		"SELECT id, name FROM xsite_store_components WHERE coefficient_group_id = %d ORDER BY name ASC LIMIT 500",
		$row->id
	));
}
if ($components && count($components)) {
?>
<div class="component_table__wrap" style="max-height:240px;overflow-y:auto">
	<div class="component_table">
		<table class="wp-list-table widefat striped xs_data_table">
			<tbody>
<?php foreach ($components as $c): ?>
				<tr class="cadre_tr">
					<td class="component_table__td-name">
						<a href="/wp-admin/admin.php?page=store&section=detail&id=<?=(int)$c->id?>" target="_blank"><?=esc_html($c->name)?></a>
					</td>
					<td class="td_delete" style="width:40px;text-align:center">
						<span class="cg_component_del" data-component-id="<?=(int)$c->id?>" data-group-id="<?=(int)$row->id?>" style="cursor:pointer;color:#c0392b" title="Отвязать от группы">×</span>
					</td>
				</tr>
<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php
} else {
?>
<div style="color:#888;padding:10px 0">Нет привязанных компонентов.</div>
<?php
}
?>

<div style="margin-top:10px">
	<div class="button" data-modal="coefficient-group-components" data-id="<?=(int)$row->id?>">+ Добавить компоненты в группу</div>
</div>

<script>
(function(){
	var $form = jQuery('#cg_tiers_form');
	var $rows = jQuery('#cg_tiers_rows');
	var group_id = <?=(int)$row->id?>;

	function nextIndex() {
		return $rows.find('tr.cg_tier_row').length;
	}

	function renumber() {
		$rows.find('tr.cg_tier_row').each(function(i){
			jQuery(this).find('td:first').text(i + 1);
			jQuery(this).find('input').each(function(){
				var name = jQuery(this).attr('name');
				if (name) {
					jQuery(this).attr('name', name.replace(/tiers\]\[\d+\]/, 'tiers][' + i + ']'));
				}
			});
		});
	}

	jQuery('#cg_tier_add').on('click', function(){
		var i = nextIndex();
		var html = '<tr class="cg_tier_row">' +
			'<td style="text-align:center;color:#888">' + (i + 1) + '</td>' +
			'<td><input type="number" name="post_data[tiers][' + i + '][min_days]" value="1" min="1" step="1" style="width:60px" required></td>' +
			'<td><input type="number" name="post_data[tiers][' + i + '][max_days]" value="" min="1" step="1" style="width:60px" placeholder="∞"></td>' +
			'<td><input type="number" name="post_data[tiers][' + i + '][k]" value="2.0" min="0.001" step="0.001" style="width:80px" required></td>' +
			'<td style="text-align:center"><input type="color" name="post_data[tiers][' + i + '][color]" value="#ffffff" style="width:40px;height:26px"></td>' +
			'<td style="text-align:center"><span class="cg_tier_del" style="cursor:pointer;color:#c0392b;font-size:18px" title="Удалить диапазон">×</span></td>' +
		'</tr>';
		$rows.append(html);
	});

	jQuery(document).on('click', '.cg_tier_del', function(){
		jQuery(this).closest('tr').remove();
		renumber();
	});

	jQuery('#cg_tiers_save').on('click', function(){
		var $btn = jQuery(this);
		var data = $form.serialize() + '&action=coefficient-group-tiers-save';
		$btn.prop('disabled', true).val('Сохраняю...');
		jQuery('#cg_tiers_result').html('');
		jQuery.post('/wp-content/themes/xsiteshop/load/admin/ajax.php', data, function(resp){
			$btn.prop('disabled', false).val('Сохранить диапазоны');
			if (resp && resp.status === 'good') {
				jQuery('#cg_tiers_result').html('<p style="color:#27ae60">' + resp.message + '</p>');
			} else {
				jQuery('#cg_tiers_result').html('<p style="color:#c0392b">' + ((resp && resp.message) || 'Ошибка сохранения') + '</p>');
			}
		}, 'json').fail(function(){
			$btn.prop('disabled', false).val('Сохранить диапазоны');
			jQuery('#cg_tiers_result').html('<p style="color:#c0392b">Ошибка сети</p>');
		});
	});

	jQuery('#cg_recompute_group').on('click', function(){
		if (!confirm('Пересчитать цены всех компонентов в группе?\n\nПересчёт идёт пачками по 50 с видимым прогресс-баром.')) return;
		var $btn = jQuery(this);
		$btn.prop('disabled', true).val('Пересчитываю...');
		jQuery('#cg_tiers_result').html('<div id="cg_group_progress"></div>');
		if (typeof window.cgBatchRecompute === 'function') {
			window.cgBatchRecompute(group_id, jQuery('#cg_group_progress'), function(res){
				$btn.prop('disabled', false).val('Пересчитать цены группы');
			});
		} else {
			// Fallback: sync-вызов (если функция не загружена — например модалка открыта с другой страницы)
			jQuery.post('/wp-content/themes/xsiteshop/load/admin/ajax.php',
				{action: 'coefficient-group-recompute', group_id: group_id},
				function(resp){
					$btn.prop('disabled', false).val('Пересчитать цены группы');
					var cls = (resp && resp.status === 'good') ? '#27ae60' : '#c0392b';
					jQuery('#cg_group_progress').html('<p style="color:' + cls + '">' + ((resp && resp.message) || 'Ошибка') + '</p>');
				}, 'json'
			).fail(function(){
				$btn.prop('disabled', false).val('Пересчитать цены группы');
				jQuery('#cg_group_progress').html('<p style="color:#c0392b">Ошибка сети или таймаут</p>');
			});
		}
	});

	jQuery(document).on('click', '.cg_component_del', function(){
		var $row = jQuery(this).closest('tr');
		var component_id = jQuery(this).data('component-id');
		if (!confirm('Отвязать компонент от группы? Он перейдёт на дефолт-коэф.')) return;
		jQuery.post('/wp-content/themes/xsiteshop/load/admin/ajax.php',
			{action: 'coefficient-group-components-del', component_id: component_id},
			function(resp){
				if (resp && resp.status === 'good') {
					$row.remove();
				} else {
					alert((resp && resp.message) || 'Ошибка');
				}
			}, 'json'
		);
	});
})();
</script>
