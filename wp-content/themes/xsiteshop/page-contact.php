<? 
get_header(); 
		
while ( have_posts() ) : the_post(); 
	?><h1><? the_title(); ?></h1><?
endwhile;

if(is_super_admin())
{
	?><a target="_blank" href="/wp-admin/admin.php?page=xs_setting&tab=main#id_Контакты" class="xs_link_edit"></a><?
}

?></div><?

if(!empty(xs_get_option("xs_places_map")))
{
	$data_places = explode(";", xs_get_option("xs_places_map"));
	
	foreach($data_places as $v)
	{
		$places[] = explode(",", $v);
	}
}

if(count($places) > 0)
{					
	?><div id="xs_map" <?=xs_get_option('xs_gray_map') ? 'class="gray"' : '' ?> style="height:<?=xs_get_option("xs_height_map", 410) ?>px"></div><?
	?><script src="https://api-maps.yandex.ru/2.1/?load=package.standard,package.clusters&lang=ru-RU" type="text/javascript"></script><?
	?><script type="text/javascript"><?

		?>ymaps.ready(init); <?

		?>function init(){<?
			?>myMap = new ymaps.Map ("xs_map", {<?
				?>center: parseInt(jQuery(window).width()) < 800 ? [<?=!empty(xs_get_option("xs_center_map_mobile")) ? trim(xs_get_option("xs_center_map_mobile")) : $places[0][0].",".$places[0][1] ?>] : [<?=!empty(xs_get_option("xs_center_map")) ? trim(xs_get_option("xs_center_map")) : $places[0][0].",".$places[0][1] ?>],<?
				?>zoom: <?=xs_get_option("xs_scale_map", 13) ?>,<?
				?>controls:  [],<?
			?>}, {<?
				?>searchControlProvider: 'yandex#search'<?
			?>});<?
							
			?>var myPlacemark = [];<?
			
			?>var clusterer = new ymaps.Clusterer({<?
				?>groupByCoordinates: false,<?
				?>clusterDisableClickZoom: false<?
			?>}); <?

			$i = 0;
			
			foreach($places as $v)
			{
				?>myPlacemark[<?=$i ?>] = new ymaps.Placemark(<?
					?>[<?=trim($v[0]) ?>,<?=trim($v[1]) ?>], <?
					?>{<? 
						?>balloonContent: '<?=$v[2] ?>',<?
						?>open:true,<?
					?>},<?
					?>{<?
						?>preset: '<?=xs_get_option("xs_color_map", "islands#darkOrangeIcon") ?>'<?
					?>}<?
				?>); <?
				
				$i++;
			}
			
			?>clusterer.add(myPlacemark); <?
			?>myMap.controls.add(new ymaps.control.ZoomControl( {options: { position: { right: 20, top: 55, left: 'auto' }}})); <?
			?>myMap.behaviors.disable('scrollZoom'); <?
			?>myMap.geoObjects.add(clusterer);<?

		?>}<?
	?></script><?
}

?><div class="container"><?		

	?><div class="xs_flex xs_wrap contact_container"><?
	
		?><div class="left_block"><?
			?><p class="description">Если у вас есть вопросы, вы можете позвонить нам или <a href="#xs_recall" rel="nofollow" class="fancybox" data-theme="Заказ обратного звонка" data-button="Заказать звонок">заказать обратный звонок</a>.</p><?
	
			?><div class="xs_flex xs_wrap xs_start contacts"><?
			
				?><div class="col"><?
				
					if(count($big_data['phones']) > 0)
					{
						?><div class="contact phone"><?
							?><span>Телефон:</span><?
							?><div><?
								foreach($big_data['phones'] as $v)
								{
									?><div><a rel="nofollow" href="tel:<?=$v ?>"><?=$v ?></a></div><?
								}
							?></div><?										
						?></div><?
					}
					
					if(count($big_data['emails']) > 0)
					{
						?><div class="contact email"><?
							?><span>Email:</span><?
							?><div><?
								foreach($big_data['emails'] as $v)
								{
									?><div><a rel="nofollow" href="mailto:<?=$v ?>"><?=$v ?></a></div><?
								}
							?></div><?
						?></div><?
					}
					
				?></div><?
				
				?><div class="col"><?
				
					if(count($big_data['work']) > 0)
					{
						?><div class="contact work"><?
							?><span>Режим работы:</span><?
							?><div><?=$big_data['work'] ?></div><?		
						?></div><?
					}
				
					if(count($big_data['address']) > 0)
					{
						$i = 0;
						
						foreach($big_data['address'] as $v)
						{
							?><div class="contact address"><?
								if($i == 0)
								{
									?><span>Адрес:</span><?
								}
								?><div><?=$v ?></div><?
							?></div><?
							
							$i++;
						}
					}
				
				?></div><?
			?></div><?
			
			the_content();		
			
			get_template_part('templates/social');
			
		?></div><?
		
		?><div class="right_block"><?
			?><div class="title">Напишите нам</div><?
			?><p class="description">Отправьте нам сообщение с интересующим вопросом.<br/>Мы ответим в ближайшее время.</p><?
			?><div class="xs_form"><?
				?><form class="xs_send_form" data-yandexid="<?=xs_get_option('xs_metrika_form-write-us')?>"><?
					?><div class="xs_result"></div><?
					?><div class="xs_flex"><?
						?><div class="input"><?
							?><input type="text" required name="xs_name" placeholder="Ваше имя" /><?
						?></div><?
						?><div class="input"><?
							?><input type="text" required name="xs_email" placeholder="Email или телефон" /><?
						?></div><?
					?></div><?
					?><textarea required placeholder="Сообщение" name="data[Сообщение]"></textarea><?
					?><input type="hidden" name="send_it" value="y" /><?
					?><input type="hidden" name="xs_theme" value="Вопрос с сайта" /><?
					?><input type="hidden" name="xs_link" value="" /><?
					
					?><div class="xs_flex xs_middle"><?
						?><div class="policy"><?
							?><input type="checkbox" id="check_question" name="check_question" required checked /><?
							?><label for="check_question">Согласен с <a href="<?=get_permalink(3017) ?>" target="_blank">политикой конфиденциальности</a></label><?
						?></div><?
						
						?><input type="submit"  value="Отправить сообщение" class="sanding btn" /><?
					?></div><?
				?></form><?
			?></div><?
		?></div><?
	?></div><?
	

get_footer();

