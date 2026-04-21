<? global $big_data;

?><div id="xs_recall" class="hide xs_recall-common"><?
	?><div class="xs_recall__inner"><?
		?><div class="title">Заказ обратного звонка</div><?
		?><p class="description">Заполните поля, нажмите кнопку "<span class="text_button">заказать звонок</span>" и наш менеджер свяжется с вами в течение 10 минут.</p><?
		?><div class="phones"><?
			if(count($big_data['phones']) > 1)
			{
				$teg_a = array("-", "(", ")", " ", "+");
				$teg_b = array("", "", "", "", "");

				$v_new = str_replace(
					$teg_a, $teg_b, $big_data['phones'][2]
				);

				?><a class="phones-item" href="tel:<?=$v_new ?>">
					<nobr><?=$big_data['phones'][2] ?></nobr>
				</a><?
			}

			?><span>или</span><?

			if(count($big_data['phones']) > 1)
			{
				$teg_a = array("-", "(", ")", " ", "+");
				$teg_b = array("", "", "", "", "");

				$v_new = str_replace(
					$teg_a, $teg_b, $big_data['phones'][1]
				);

				?><a class="phones-item" href="tel:<?=$v_new ?>">
					<nobr><?=$big_data['phones'][1] ?></nobr>
				</a><?
			}
		?></div><?

		if(xs_get_option('xs_is_hide_messenger') != 'on')
		{
			?><p class="mess-descr"><?

				if(!empty(xs_get_option('xs_social_link_wa')))
				{
					?><a class="messenger-icon mess-descr-whatsapp" href="https://wa.clck.bar/<?=xs_get_option('xs_social_link_wa') ?>?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5.%20%D0%A3%20%D0%BC%D0%B5%D0%BD%D1%8F%20%D0%B5%D1%81%D1%82%D1%8C%20%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81:" target="_blank">
						<svg class="icon-whatsapp">
							<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-whatsapp">
						</svg>
					</a><?
				}

				if(!empty(xs_get_option('xs_social_link_teleg')))
				{
					?><a class="messenger-icon mess-descr-telegram" href="https://t.me/<?=xs_get_option('xs_social_link_teleg')?>" target="_blank">
						<svg class="icon-telegram">
							<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-telegram">
						</svg>
					</a><?
				}

				?><span>Жми и оформи заказ в мессенджере!</span>
			</p><?
		}
		
		/*
		?><script data-b24-form="inline/49/bgc7p5" data-skip-moving="true">
			(function(w,d,u){
			var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);
			var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
			})(window,document,'https://cdn-ru.bitrix24.ru/b16339906/crm/form/loader_49.js');
		</script><?
		*/

		?><form class="xs_send_form" data-yandexid=""><?
			?><div class="xs_result"></div><?
			?><input type="text" required name="xs_name" placeholder="Ваше имя" /><?
			?><input type="text" required name="xs_phone" placeholder="+7 (___) ___-__-__" /><?
			?><input type="hidden" name="send_it" value="y" /><?
			?><input type="hidden" name="xs_theme" value="" /><?
			?><input type="hidden" name="xs_link" value="" /><?
			?><div class="policy"><?
				?><input type="checkbox" id="check1" name="check1" required checked /><?
				?><label for="check1">Согласен с <a href="<?=get_permalink(3444) ?>" target="_blank">политикой конфиденциальности</a></label><?
			?></div><?
			?><input type="submit"  value="Оставить заявку" class="sanding btn" /><?
		?></form><?
		
		?><div class="shedule">
			<p class="shedule-top">Прием заказов с 8:00 до 22:00</p>
			<p class="shedule-down">(Если доставка была оформлена в ночное время, то заказ обрабатывается менеджерами в 8:00)</p> 
		</div><?
	?></div><?
?></div><? 

?><div id="xs_recall_one_click" class="hide xs_recall-common"><?
	?><div class="xs_recall__inner"><?
		?><div class="title">Заказ обратного звонка</div><?
		?><p class="description">Заполните поля, нажмите кнопку "<span class="text_button">заказать звонок</span>" и наш менеджер свяжется с вами в течение 10 минут.</p><?
		?><div class="phones"><?
			if(count($big_data['phones']) > 1)
			{
				$teg_a = array("-", "(", ")", " ", "+");
				$teg_b = array("", "", "", "", "");

				$v_new = str_replace(
					$teg_a, $teg_b, $big_data['phones'][2]
				);

				?><a class="phones-item" href="tel:<?=$v_new ?>">
					<nobr><?=$big_data['phones'][2] ?></nobr>
				</a><?
			}

			?><span>или</span><?

			if(count($big_data['phones']) > 1)
			{
				$teg_a = array("-", "(", ")", " ", "+");
				$teg_b = array("", "", "", "", "");

				$v_new = str_replace(
					$teg_a, $teg_b, $big_data['phones'][1]
				);

				?><a class="phones-item" href="tel:<?=$v_new ?>">
					<nobr><?=$big_data['phones'][1] ?></nobr>
				</a><?
			}
		?></div><?

		if(xs_get_option('xs_is_hide_messenger') != 'on')
		{
			?><p class="mess-descr"><?

				if(!empty(xs_get_option('xs_social_link_wa')))
				{
					?><a class="messenger-icon mess-descr-whatsapp" href="https://wa.clck.bar/<?=xs_get_option('xs_social_link_wa') ?>?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5.%20%D0%A3%20%D0%BC%D0%B5%D0%BD%D1%8F%20%D0%B5%D1%81%D1%82%D1%8C%20%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81:" target="_blank">
						<svg class="icon-whatsapp">
							<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-whatsapp">
						</svg>
					</a><?
				}

				if(!empty(xs_get_option('xs_social_link_teleg')))
				{
					?><a class="messenger-icon mess-descr-telegram" href="https://t.me/<?=xs_get_option('xs_social_link_teleg')?>" target="_blank">
						<svg class="icon-telegram">
							<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-telegram">
						</svg>
					</a><?
				}
 
				?><span>Жми и оформи заказ в мессенджере!</span>
			</p><?
		}
		
		/*
		?><script data-b24-form="inline/81/d327w1" data-skip-moving="true">
			(function(w,d,u){
			var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);
			var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
			})(window,document,'https://cdn-ru.bitrix24.ru/b16339906/crm/form/loader_81.js');
		</script><?
		*/

		?><form class="xs_send_form" data-yandexid=""><?
			?><div class="xs_result"></div><?
			?><input type="text" required name="xs_name" placeholder="Ваше имя" /><?
			?><input type="text" required name="xs_phone" placeholder="+7 (___) ___-__-__" /><?
			?><input type="hidden" name="send_it" value="y" /><?
			?><input type="hidden" name="xs_theme" value="" /><?
			?><input type="hidden" name="xs_link" value="" /><?
			?><input type="hidden" name="xs_comment" value="" /><?
			?><div class="policy"><?
				?><input type="checkbox" id="check1" name="check1" required checked /><?
				?><label for="check1">Согласен с <a href="<?=get_permalink(3444) ?>" target="_blank">политикой конфиденциальности</a></label><?
			?></div><?
			?><input type="submit"  value="Оставить заявку" class="sanding btn" /><?
		?></form><?
		
		?><div class="shedule">
			<p class="shedule-top">Прием заказов с 8:00 до 22:00</p>
			<p class="shedule-down">(Если доставка была оформлена в ночное время, то заказ обрабатывается менеджерами в 8:00)</p> 
		</div><?
	?></div><?
?></div><? 
