<div class="wr_found"><?
	?><div class="container"><?
		?><div class="found"><?

			?><div class="inner xs_flex xs_middle"><?
				?><div class="image"></div><?
				?><div class="text"><?
					?><div class="quest">Не нашли нужного товара?</div><?
					?><div class="added">Заполните необходимые поля, описав нужный вам товар. Наши специалисты помогу в ближайшее время.</div><?
				?></div><?
				?><div class="form xs_form"><?
					?><form class="xs_send_form" data-yandexid="<?=xs_get_option('xs_metrika_form-find-tovar')?>"><?
						?><div class="xs_flex"><?
							?><div class="input"><?
								?><input type="text" required name="xs_name" placeholder="Ваше имя" /><?
							?></div><?
							?><div class="input"><?
								?><input type="text" required name="xs_email" placeholder="E-mail или телефон" /><?
							?></div><?
						?></div><?
						?><textarea type="text" required placeholder="Пожалуйста, опишите нужный товар" class="describe" name="data[Описание товара]"></textarea><?
						?><input type="hidden" name="send_it" value="y" /><?
						?><input type="hidden" name="xs_theme" value="Не нашли нужного товара" /><?
						?><input type="hidden" name="xs_link" value="" /><?
						?><div class="xs_flex xs_middle"><?
							?><div class="policy"><?
								?><input type="checkbox" id="check_finder" name="check_finder" required checked /><?
								?><label for="check_finder">Согласен с <a href="<?=get_permalink(3017) ?>" target="_blank">политикой конфиденциальности</a></label><?
							?></div><?
							?><input type="submit"  value="Отправить сообщение" class="sanding btn shadow-hover" /><?
						?></div><?
						?><div class="xs_result"></div><?
					?></form><?
				?></div><?
			?></div><?

		?></div><?
	?></div><?
?></div><?
