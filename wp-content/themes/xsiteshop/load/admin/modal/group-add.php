<?defined("IS_CHECK") || exit;

?><div class="admin_modal__title">Новая группа компонентов</div>
<form class="xs_ajax_form" action="#" data-action="group-add" data-datatype="json" method="post"><?
	
	xs_input('name', '', 'Название', ['required' => true]);				
	xs_input('sort', '', 'Сортировка', ['type' => 'number', 'min' => 0, 'step' => 1]);
	
	?><br>
	<div class="xs_form_result"></div>
	<div class="admin_modal__buttons">
		<input class="button-primary" type="submit" value="Создать группу">	
	</div>
</form>