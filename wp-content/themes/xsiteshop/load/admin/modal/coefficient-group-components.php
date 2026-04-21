<?defined("IS_CHECK") || exit;

$group_id = isset($_REQUEST['id']) ? (int)xs_format($_REQUEST['id']) : 0;
if (!$group_id || !($cg = get_coefficient_group($group_id, false)))
	die("Группа коэффициентов не найдена.");

$xs_filter = isset($_REQUEST['filter']) ? xs_format($_REQUEST['filter']) : [];
$ar_where = ["`coefficient_group_id` = '0' OR `coefficient_group_id` != '" . $group_id . "'"];
$setFilter = false;

if (isset($xs_filter['search']) && !empty($xs_filter['search'])) {
	$search_like = '%' . str_replace(" ", "%", xs_format($xs_filter['search'])) . '%';
	$ar_where[] = $wpdb->prepare("(`name` LIKE %s OR `name_for_user` LIKE %s)", $search_like, $search_like);
	$setFilter = true;
}

if (isset($xs_filter['no_coef_group']) && $xs_filter['no_coef_group'] == 'y') {
	$ar_where[] = "`coefficient_group_id` = '0'";
	$setFilter = true;
}

$where = "WHERE (" . implode(") AND (", $ar_where) . ")";
$xs_query = "SELECT `id`, `name`, `image`, `coefficient_group_id` FROM `xsite_store_components` " . $where;

$big_data['number'] = 50;
$big_data['offset'] = ($big_data['paged'] - 1) * $big_data['number'];

$components = $wpdb->get_results($xs_query . get_order_limit('name', 'asc'));
$xs_total = $wpdb->get_var(preg_replace('|(SELECT).+(FROM)|isU', "$1" . " COUNT(*) " . "$2", $xs_query));

?>
<div class="admin_modal__title">Добавить компоненты в группу «<?=esc_html($cg->name) ?>»</div>

<div id="xs_filter__result">
	<form class="xs_filter xs_ajax_form" method="get" action="<?=$big_data['current_url'] ?>" data-result="xs_filter__result">
		<label class="long">
			<input type="text" name="filter[search]" value="<?=esc_attr(isset($xs_filter['search']) ? $xs_filter['search'] : '') ?>" placeholder="Поиск по названию">
		</label>
		<label>
			<input onchange="jQuery(this).parents('.xs_filter').submit()" type="checkbox" name="filter[no_coef_group]"<?=isset($xs_filter['no_coef_group']) && $xs_filter['no_coef_group'] == 'y' ? " checked" : ""?> value="y"> Только без группы коэф.
		</label>

		<input type="hidden" name="id" value="<?=(int)$group_id ?>">
		<input type="hidden" name="modal" value="coefficient-group-components">

		<label class="xs_submit">
			<input type="submit" value="Искать" class="button-primary" />
		</label>

		<?php if ($setFilter): ?>
			&nbsp;<a href="#" onclick="jQuery(this).parents('.xs_filter').find('input[type=text]').val(''); jQuery(this).parents('.xs_filter').find('input[type=checkbox]').prop('checked', false); jQuery(this).parents('.xs_filter').submit(); return false">× очистить</a>
		<?php endif; ?>

		<input type="hidden" value="1" name="paged" />
	</form>

	<div class="content_container" style="margin-top:10px">
		<div id="xs_current_page" style="display:none"><?=$big_data['paged'] ?></div>
		<form class="xs_ajax_form" action="#" data-action="coefficient-group-components-add" data-datatype="json" method="post">
<?php if (is_array($components) && count($components)): ?>
			<div class="component_table">
				<table class="wp-list-table widefat striped xs_data_table xs_users">
					<thead>
						<tr>
							<td style="width:30px"><input id="cg_cb_all" type="checkbox"></td>
							<td style="width:50px">Фото</td>
							<td>Наименование</td>
							<td style="width:160px">Текущая группа коэф.</td>
						</tr>
					</thead>
					<tbody>
<?php foreach ($components as $c): ?>
						<tr class="cadre_tr">
							<td>
								<input type="checkbox" name="post_data[components][<?=(int)$c->id ?>]" value="y" class="cg_cb">
							</td>
							<td class="component_table__td-image">
								<span class="component_table__image"<?=!empty($c->image) ? ' style="background-image:url(' . esc_attr($big_data['component_image_path'] . $c->image) . ')"' : ""?>></span>
							</td>
							<td>
								<a href="/wp-admin/admin.php?page=store&section=detail&id=<?=(int)$c->id ?>" target="_blank"><?=esc_html($c->name) ?></a>
							</td>
							<td>
<?php
								if ((int)$c->coefficient_group_id > 0) {
									$other = get_coefficient_group((int)$c->coefficient_group_id, false);
									echo $other ? '<span style="color:#888">' . esc_html($other->name) . '</span>' : '<span style="color:#999">—</span>';
								} else {
									echo '<span style="color:#999">нет</span>';
								}
?>
							</td>
						</tr>
<?php endforeach; ?>
					</tbody>
				</table>
			</div>
<?php else: ?>
			<p style="color:#888;padding:10px 0">Компонентов не найдено.</p>
<?php endif; ?>
			<div class="clear"></div>
			<div class="xs_form_result"></div>

			<div class="xs_pages xs_flex xs_middle" style="margin-top:10px">
<?php
				echo paginate_links([
					'base'      => str_replace(['&paged=0', '?paged=0'], '', setUrl($big_data['current_url'], 'paged', '0')) . '%_%',
					'format'    => '&paged=%#%',
					'current'   => $big_data['paged'],
					'total'     => ceil($xs_total / $big_data['number']),
					'prev_next' => false,
					'type'      => 'list',
				]);

				xs_input('group_id', $group_id, '', ['type' => 'hidden']);
?>
				<div class="xs_flex xs_middle" style="margin-left:auto">
					<input type="submit" class="button-primary" value="Привязать выбранные" />
				</div>
			</div>
		</form>
	</div>
</div>

<script>
jQuery('#cg_cb_all').on('change', function(){
	jQuery('.cg_cb').prop('checked', this.checked);
});
</script>
