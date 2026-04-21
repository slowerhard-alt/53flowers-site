<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

$product_id = (int)$_GET['product_id'];

?><div class="xs_recall-common xs_recall-common--set_structure">
	<div class="xs_recall__inner"><?
		?><div class="title">Изменение состава товара</div><?
		?><p class="description">Опишите необходимые изменения и отправьте заявку на изменение.</p>
		
		<form class="xs_send_form" data-yandexid=""><?
			?><textarea required name="xs_comment"></textarea><?
			?><input type="hidden" name="send_it" value="y"><?
			?><input type="hidden" name="xs_theme" value="Изменение состава товара"><?
			?><input type="hidden" name="xs_link" value="<?=get_permalink($product_id) ?>"><?
			?><input type="hidden" name="product_id" value="<?=$product_id ?>"><?
			?><div class="xs_result"></div><?
			?><input type="submit"  value="Отправить" class="sanding btn" /><?
		?></form>
	</div>
</div>