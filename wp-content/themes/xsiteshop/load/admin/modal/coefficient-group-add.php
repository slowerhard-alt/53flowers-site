<?defined("IS_CHECK") || exit;

?><div class="admin_modal__title">Новая группа коэффициентов</div>
<form class="xs_ajax_form" action="#" data-action="coefficient-group-add" data-datatype="json" method="post"><?

	xs_input('name', '', 'Название (напр. "Зелень", "Розы стандарт")', ['required' => true]);
	xs_input('color', '#ffffff', 'Цвет для подсветки', ['type' => 'color']);
	xs_input('sort', 0, 'Сортировка', ['type' => 'number', 'min' => 0, 'step' => 1]);

	?><br>
	<div class="xs_form_result"></div>
	<div class="admin_modal__buttons">
		<input class="button-primary" type="submit" value="Создать группу">
	</div>
</form>
