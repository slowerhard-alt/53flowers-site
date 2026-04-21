<?
global $big_data;

?><a href="tel:<?=$big_data['phones'][0]?>" id="callbackMobile"></a><?
?><div id="callback" class="cbh-phone cbh-green cbh-show cbh-static"><?
	?><div class="cbh-ph-circle"></div><?
	?><div class="cbh-ph-circle-fill"></div><?
	?><div class="cbh-ph-img-circle"></div><?
?></div><?
?><table class="fonCallBack" data-time="<?=xs_get_option('xs_recall_button_time') ?>"><?
	?><tr><?
		?><td><?
			?><div class="oknoCallBack"><?
				?><div class="policy"><?
					?><label><?
						?><input type="checkbox" name="policy" checked  /> Нажимая на кнопку "Жду звонка!" я даю <a href="<?=get_permalink(3017) ?>" target="_blank">согласие на обработку персональных данных</a><?
					?></label><?
				?></div><?
				?><div class="exitCallBack"></div><?
				?><form class="clbh_banner-body recall" action="<? bloginfo('template_url'); ?>/load/xs_callback.php" method="post"><?
					?><div class="clbh_banner-h1"><?
						?><span class="timeCallBack">Вы находитесь на данной странице уже более <span id="timeCallBack"></span> секунд. <?=xs_get_option('xs_recall_button_text') ?></span><?
						?><span class="textClick"><?=xs_get_option('xs_recall_button_text_click') ?></span><?
					?></div><?
					?><div class="clbh_banner-form-row-1"><?
						?><div class="clbh_banner-form"><?
							?><div class="clbh_banner-arrow"></div><?
							?><div class="clbh_phone_line"><?
								?><input name="phone" id="xs_phone" class="clbh_banner-textbox" type="text" placeholder="Введите ваш телефон" maxlength="18" /><?
								?><button class="clbh_banner-button">Жду звонка!</button><?
							?></div><?
						?></div><?
					?></div><?
				?></form><?
			?></div><?
		?></td><?
	?></tr><?
?></table><? 
