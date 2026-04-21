<?defined("IS_CHECK") || exit;

if(!($row = get_group_components(isset($get_data['id']) ? $get_data['id'] : 0)))
{
	echo "Группа компонентов не найдена.";
	die();
}

?><div class="admin_modal__title">Группа компонентов «<?=esc_html($row->name) ?>»</div><?

xs_get_message();

?><form class="xs_ajax_form" action="#" data-action="group-edit" data-datatype="json" method="post"><?
	
	xs_input('name', $row->name, 'Название группы', ['required' => true]);				
	xs_input('sort', $row->sort, 'Сортировка', ['type' => 'number', 'min' => 0, 'step' => 1]);
	xs_input('id', $row->id, '', ['type' => 'hidden']);
	
	?><br>
	<div class="xs_form_result"></div>
	<div class="admin_modal__buttons">
		<input class="button-primary" type="submit" value="Сохранить изменения">
		<span class="admin_modal__delete">Удалить группу</span>	
	</div>
</form>

<br><br>
<div class="admin_modal__subtitle">Компоненты группы</div><?

if(count($row->components))
{
	?><div class="component_table__wrap">
		<div class="component_table">
			<table class="wp-list-table widefat striped xs_data_table xs_users">
				<tbody><? 
				
				foreach($row->components as $v)
				{
					$v->component_id = $v->id;
					
					?><tr class="cadre_tr">
						<td class="component_table__td-image">
							<span class="component_table__image"<?=!empty($v->image) ? ' style="background-image:url('.esc_attr($big_data['component_image_path'].$v->image).')"' : "" ?>></span>
						</td>
						<td class="component_table__td-name">
							<div class="component_table__name">
								<a href="/wp-admin/admin.php?page=store&section=detail&id=<?=(int)$v->component_id ?>" target="_blank"><?=esc_html($v->name) ?></a>
							</div>
						</td>
						<td class="td_delete delete--component-to-group" data-action="group-components-del" data-component_id="<?=(int)$v->id ?>" data-group_id="<?=(int)$row->id ?>">
							<span class="xs_red dashicons-before dashicons-trash" title="Удалить"></span>
						</td>
					</tr><?  
				}
				
				?></tbody>
			</table>
		</div>
		<br>
	</div><?
}

?><form class="xs_ajax_form" action="#" data-action="group-components-del-all" data-datatype="json" method="post">
	<div class="xs_form_result"></div>
	<div class="admin_modal__buttons">
		<div class="button" data-modal="group-components" data-id="<?=(int)$row->id ?>">+ Добавить компоненты в группу</div><?
		
		if(count($row->components) > 0)
		{
			?><span class="admin_modal__delete delete--components-to-group">Очистить все</span><?
		}
		
	?></div><?
	
	xs_input('id', $row->id, '', ['type' => 'hidden']);
	
?></form>