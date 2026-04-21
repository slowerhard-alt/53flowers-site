<?
global $big_data;

	if(!$big_data['is_front_page'])
	{
		?></div><?
	?></div><?
	}


	/* Мобильное меню START */
	if($big_data['device']['is_mobile'])
	{
		?><div class="mobile-nav">
			<div class="mobile-nav__item mobile-nav__item--catalog" aria-label="mob-catalog">
				<span class="mobile-nav__icon mobile-nav__icon--catalog"></span>
				<span class="mobile-nav__tlt">Каталог</span>
			</div>
			<a class="mobile-nav__item" href="<?=get_term_link(231, 'product_cat') ?>">
				<span class="mobile-nav__icon mobile-nav__icon--sale"></span>
				<span class="mobile-nav__tlt">Акции</span>
			</a>

			<a class="mobile-nav__item" href="<?=get_term_link(234, 'product_cat') ?>">
				<span class="mobile-nav__icon mobile-nav__icon--roses"></span>
				<span class="mobile-nav__tlt">Розы</span>
			</a>

			<a class="mobile-nav__item" href="<?=get_term_link(233, 'product_cat') ?>">
				<span class="mobile-nav__icon mobile-nav__icon--bukety"></span>
				<span class="mobile-nav__tlt">Букеты</span>
			</a>

			<a class="mobile-nav__item" href="<?=get_term_link(444, 'product_cat') ?>">
				<span class="mobile-nav__icon mobile-nav__icon--dessert"></span>
				<span class="mobile-nav__tlt">Десерты</span>
			</a><?
			
			/*
			<a class="mobile-nav__item" href="<? the_permalink(3452) ?>">
				<span class="mobile-nav__icon mobile-nav__icon--reviews"></span>
				<span class="mobile-nav__tlt">Отзывы</span>
			</a>
			
			if(count($big_data['phones']) > 1)
			{
				$teg_a = array("-", "(", ")", " ", "+");
				$teg_b = array("", "", "", "", "");

				$v_new = str_replace(
					$teg_a, $teg_b, $big_data['phones'][2]
				);

				?><a class="mobile-nav__item" href="tel:<?=$v_new ?>" rel="nofollow">
					<span class="mobile-nav__icon mobile-nav__icon--phone"></span>
					<span class="mobile-nav__tlt">Позвонить</span>
				</a><?
			}
			*/
			
			/*
			<a class="mobile-nav__item fancybox" href="#xs_recall" data-theme="Заказ обратного звонка" data-yandexid="zozhead" data-button="Заказать звонок" rel="nofollow">
				<span class="mobile-nav__icon mobile-nav__icon--phone"></span>
				<span class="mobile-nav__tlt">Позвонить</span>
			</a><?
			*/
			
		?></div><?
	}
	/* Мобильное меню END */


	
	?><!-- Футер START -->
	<footer class="footer footer--tune slasher-wide">
		<div class="container">
			<div class="footer__inner">
				<div class="footer__top slasher slasher-wide">
					<div class="footer__menu">
						<div class="footer__menu-ahead">Заказ и оплата</div><?
							wp_nav_menu(array(
								'theme_location' => 'menu-zakaz-i-oplata',
								'items_wrap'     => '<ul class="footer__ul">%3$s</ul>',
								'container'	  	 => false,
								'item_spacing'	 => 'discard'
							));
					?></div>
					<div class="footer__menu">
						<div class="footer__menu-ahead">Доставка цветов</div><?
							wp_nav_menu(array(
								'theme_location' => 'menu-dostavka-i-oplata',
								'items_wrap'     => '<ul class="footer__ul">%3$s</ul>',
								'container'	  	 => false,
								'item_spacing'	 => 'discard'
							));
					?></div>
					<div class="footer__menu">
						<div class="footer__menu-ahead">Наши контакты</div><?
							wp_nav_menu(array(
								'theme_location' => 'menu-nashi-kontakti',
								'items_wrap'     => '<ul class="footer__ul">%3$s</ul>',
								'container'	  	 => false,
								'item_spacing'	 => 'discard'
							));
					?></div>
					<div class="footer__contact">
						<div class="footer__contact-calls">
							<div class="footer__contact-line"><?

								if(count($big_data['phones']) > 0)
								{

								$teg_a = array("-", "(", ")", " ", "+");
								$teg_b = array("", "", "", "", "");

								$v_new = str_replace(
									$teg_a, $teg_b, $big_data['phones'][0]
								);
									?><a class="footer__contact-phone" href="tel:<?=$v_new ?>"><nobr><?=$big_data['phones'][0] ?></nobr></a><?
								}
								  
								?><a class="footer__contact-call fancybox" href="#xs_recall" data-theme="Заказ обратного звонка" data-button="Заказать">звонок бесплатный</a>
							</div>
							<div class="footer__contact-line"><?
								if(count($big_data['phones']) > 1)
								{

								$teg_a = array("-", "(", ")", " ", "+");
								$teg_b = array("", "", "", "", "");

								$v_new = str_replace(
									$teg_a, $teg_b, $big_data['phones'][1]
								);
									?><a class="footer__contact-phone footer__contact-phlast" href="tel:<?=$v_new ?>"><nobr><?=$big_data['phones'][1] ?></nobr></a><?
								}

								if(xs_get_option('xs_is_hide_messenger') != 'on')
								{
									?><div class="messengers"><?
									
										if(!empty(xs_get_option('xs_social_link_wa')))
										{
											?><a class="messenger-icon footer__messenger-icon messenger-whatsapp" href="https://wa.clck.bar/<?=xs_get_option('xs_social_link_wa') ?>?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5.%20%D0%A3%20%D0%BC%D0%B5%D0%BD%D1%8F%20%D0%B5%D1%81%D1%82%D1%8C%20%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81:" target="_blank">
												<svg class="icon-whatsapp">
													<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-whatsapp">
												</svg>
											</a><?
										}
										
										if(!empty(xs_get_option('xs_social_link_teleg')))
										{
											?><a class="messenger-icon footer__messenger-icon messenger-telegram" href="https://t.me/<?=xs_get_option('xs_social_link_teleg')?>" target="_blank">
												<svg class="icon-telegram">
													<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-telegram">
												</svg>
											</a><?
										}
										
										if(!empty(xs_get_option('xs_social_link_max')))
										{
											?><a class="messenger-icon footer__messenger-icon messenger-max" href="<?=xs_get_option('xs_social_link_max')?>" target="_blank">
												<img class="icon-max" src="<?php bloginfo('template_url'); ?>/images/icons/icon-max.svg" alt="MAX" width="28" height="28" loading="lazy">
											</a><?
										}
										
									?></div><?
								}
								
							?></div>
						</div>

						<span class="footer__contact-copy">© 2008 - <?=date('Y') ?> Интернет магазин Цветы.ру</span>
					</div>
				</div>
				<div class="footer__bottom">
					<div class="footer__side footer__pay">
						<div class="footer__wr-ways">
							<div class="footer__bottom-tlt">Способы оплаты</div>
							<ul class="footer__ways">
								<li class="footer__ways-li">Банковской картой</li>
								<li class="footer__ways-li">Наличными</li>
								<li class="footer__ways-li">Перевод через СБП</li>
								<li class="footer__ways-li">Оплата на расчетный счет</li>
							</ul>
						</div><?
						/*
						<div class="footer__accept">
							<div class="footer__accept-wrap">
								<picture>
									<source type="image/webp" srcset="<?php bloginfo('template_url'); ?>/images/pics/pay-logos.webp">
									<img class="footer__accept-img" src="<?php bloginfo('template_url'); ?>/images/pics/pay-logos.jpg" alt="picture" width="300" height="33" loading="lazy">
								</picture>
							</div>
							<div class="footer__accept-list">
								<div class="footer__accept-wrimg">
									<picture>
										<source type="image/webp" srcset="<?php bloginfo('template_url'); ?>/images/pics/pay-pay.webp">
										<img class="footer__accept-img" src="<?php bloginfo('template_url'); ?>/images/pics/pay-pay.jpg" alt="picture" width="106" height="54" loading="lazy">
									</picture>
								</div>	
								<div class="footer__accept-wrimg">
									<picture>
										<source type="image/webp" srcset="<?php bloginfo('template_url'); ?>/images/pics/cash.webp">
										<img class="footer__accept-img" src="<?php bloginfo('template_url'); ?>/images/pics/cash.jpg" alt="picture" width="91" height="43" loading="lazy">
									</picture>
								</div>
								<div class="footer__accept-wrimg">
									
								</div>
							</div>
						</div>
						*/
						
					?></div>
					<div class="footer__side footer__info"><?
						
						if(!empty(xs_get_option('xs_delivery')))
						{
							?><div class="footer__info-chart">
								<div class="footer__bottom-tlt">Доставка</div>
								<div class="footer__info-txt"><?=xs_get_option('xs_delivery') ?></div>
							</div><?
						}
						
					?></div>
					<div class="footer__side footer__info">					
						<div class="footer__info-chart">
							<div class="footer__bottom-tlt">Стоимость доставки</div>
							<div class="footer__info-txt"><?
							
							if(!empty(xs_get_option('xs_free_deli')))
							{
								?><p><?=xs_get_option('xs_free_deli') ?></p><?
							}
							if(!empty(xs_get_option('xs_charge_deli')))
							{
								?><p><?=xs_get_option('xs_charge_deli') ?></p><?
							}
							if(!empty(xs_get_option('xs_addit_text')))
							{
								?><p><?=xs_get_option('xs_addit_text') ?></p><?
							}
							
							?></div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</footer>
	<!-- Футер END --><?

	?><a class="mobile-scroll xs_hash" href="#header">
		<svg class="icon-scroll-top">
			<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg?v=3#icon-scroll-top">
		</svg>
	</a><?

	get_template_part('templates/recall');
	
	if(xs_get_option('xs_recall_button_show'))
		get_template_part('templates/callback');

?></div> 


<script>
(function(w,d,u){
var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
})(window,document,'https://cdn-ru.bitrix24.ru/b16339906/crm/tag/call.tracker.js');
</script>
				
<script>
(function(w,d,u){
        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
})(window,document,'https://cdn-ru.bitrix24.ru/b16339906/crm/site_button/loader_1_7qfz1a.js');
</script>

<script><?
	?>var head = document.head, <?
	?>link = document.createElement('link');<?
	?>link.type = 'text/css';<?
	?>link.rel = 'stylesheet';<?
	?>link.href = '<? bloginfo('template_url'); ?>/css/pending.css';<?
	?>head.appendChild(link)<?
?></script><?

if(xs_get_option('xs_recall_button_show'))
{
	?><script><?
		?>var head = document.head, <?
		?>link = document.createElement('link');<?
		?>link.type = 'text/css';<?
		?>link.rel = 'stylesheet';<?
		?>link.href = '<? bloginfo('template_url'); ?>/css/callback.css';<?
		?>head.appendChild(link)<?
	?></script><?
}

if($big_data['is_front_page'])
{	
	?><script id='wc-price-slider-js-extra'>var woocommerce_price_slider_params = {"currency_format_num_decimals":"0","currency_format_symbol":"\u0440\u0443\u0431.","currency_format_decimal_sep":",","currency_format_thousand_sep":" ","currency_format":"%v\u00a0%s"};</script><?
}

wp_footer(); 

// Скрываем не активные атрибуты из фильтра

global $wpdb;

$hide_terms = $wpdb->get_results("
	SELECT 
		t.`term_id`,
		t.`taxonomy`,
		term.`name`,
		term.`slug`
	FROM 
		`xsite_term_taxonomy` t
	LEFT JOIN `xsite_termmeta` tm ON tm.`term_id` = t.`term_id` AND tm.`meta_key` = 'disabled'
	LEFT JOIN `xsite_terms` term ON term.`term_id` = t.`term_id`
	WHERE
		t.`taxonomy` LIKE 'pa_%' AND
		tm.`meta_value` = 'y'
");

?><script>function hide_disabled_term_filter(){<?

		if($hide_terms)
		{
			foreach($hide_terms as $v)
			{
				?>jQuery(".woocommerce-widget-layered-nav-list__item a[href*='filter_<?=str_replace('pa_', '', $v->taxonomy) ?>=<?=$v->slug ?>']").parents('.woocommerce-widget-layered-nav-list__item').hide();<?
			}
		}
		
	?>}<?
	
	if($hide_terms)
	{
		?>jQuery(function($){hide_disabled_term_filter()})<?
	}

?></script><?

// Показываем всплывающее сообщение 

if(!isset($_COOKIE['info_message']) || $_COOKIE['info_message'] != 'hide')
{
	if(xs_get_option('xs_show_message') == 'on')
	{
		?><div id="info_message"><?=nl2br(xs_get_option('xs_text_message')) ?></div>
		
		<script>
			
			jQuery(function($)
			{
				$.fancybox.open({
					src: '#info_message',
					type: 'inline',
					opts : {
						afterClose: function( instance, current ) 
						{
							$.cookie('info_message', 'hide', { expires: 7, path: '/' });
						}
					}
				})
			})
			
		</script><?
	}
}

?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(41837129, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/41837129" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter --></body><?
?></html><?