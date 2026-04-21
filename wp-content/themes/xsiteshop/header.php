<?
global $big_data;

do_action('xs_template_init');

?><!DOCTYPE html><?
?><html lang="ru-RU"><?
?><head><?
	?><title><? wp_title('', true, 'right'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-control" content="public">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?=($xs_favicon = xs_get_option('xs_favicon')) ? wp_get_attachment_url($xs_favicon) : get_bloginfo('template_directory').'/favicon.png';?>"><? 
		
	wp_head();
	
	$body_class = [];
	
	if(is_admin_bar_showing())
		$body_class[] = 'show_admin_bar';
	
	if(!is_front_page())
		$body_class[] = 'not_front';
	
	if(is_user_logged_in())
		$body_class[] = 'login_user';
	
?>
	<meta name="facebook-domain-verification" content="urfw70wrnd2bfisls4sbveuicqevu8">
	<meta name="yandex-verification" content="a08a5e9e88f31558">
	<meta name="google-site-verification" content="sIE9Zfss7v66TCPPh7r4PPIZCzXIN8p1WuLbzHA-y1E" />
</head>
<body class="<?=implode(' ', $body_class) ?>">
<?php if (function_exists("wp_body_open")) wp_body_open(); ?>

	
	
	<div class="overflow">
		<!-- Хедер START -->
		<header class="header" id="header"><?
		
		if( !isset($_COOKIE['salesline']) || $_COOKIE['salesline'] != 'hide')
		{
			$salesline = get_posts(
				array(
					'post_type'       => 'salesline',
					'post_status'     => 'publish'
				) 
			);

			if(count($salesline) > 0)
			{
				?><!-- Акционный слайдер в шапке START -->
				<div class="salesline">
					<div class="salesline__slider"><?
					foreach ( $salesline as $v ) 
					{ 
						$xs_options = get_post_meta($v->ID, 'xs_options', true);
						
						?><div class="salesline__slider-item"<?/* style="background-color: <? echo $xs_options['color']; ?>;"*/ ?>>
							<div class="container">
								<div class="salesline__slider-slide"><?

								if(!empty($xs_options['link']))
								{
									$xs_options = get_post_meta($v->ID, 'xs_options', true);

									?><a class="salesline__slider-txt" href="<? echo $xs_options['link'] ?>" <? if($xs_options['target'] == 'y') echo 'target="_blank" ' ?>><?
										echo $v->post_title;
									?></a><?

									if( isset($xs_options['more_text']) && !empty($xs_options['more_text']) )
									{
										?><a class="salesline__slider-btn" href="<? echo $xs_options['link'] ?>" <? if($xs_options['target'] == 'y') echo 'target="_blank" ' ?>><?
											echo $xs_options['more_text'];
										?></a><?
									}

								}
								else
								{
									?><div class="salesline__slider-txt"><?
										echo $v->post_title;
									?></div><?
								}
								?></div>
							</div>
						</div><?
					}
					?></div>
					<div class="salesline-close"></div>
				</div>
				<!-- Акционный слайдер в шапке END --><?
			}
		}

		?><div class="container">
			<div class="header__inner">
				<div class="header__xtop xtop">
					<a class="xtop__logo" href="<?=esc_url(home_url( '/' )) ?>">
						<picture>
							<source type="image/webp" srcset="<?php bloginfo('template_url'); ?>/images/pics/logo.webp">
							<img src="<?php bloginfo('template_url'); ?>/images/pics/logo.jpg" alt="Логотип Цветы.ру — доставка цветов в Великом Новгороде" width="180" height="77">
						</picture>
					</a>

					<div class="xtop__mobile-top mobile-top">
						<a class="mobile-top__logo xlogo" href="<?=esc_url(home_url( '/' )) ?>">
							<div class="xlogo__image">
								<img src="<?php bloginfo('template_url'); ?>/images/pics/mobile-logo.png" alt="Логотип Цветы.ру — доставка цветов в Великом Новгороде" width="77" height="109">
							</div>

							<div class="xlogo__text">
								<strong>Цветы.ру</strong>
								<span>Великий Новгород</span>
							</div>
						</a>

						<div class="mobile-top__basket">
							<div class="header__basket" data-load="xs_cart">
								<? get_template_part('load/xs_cart_new');?>
							</div>
						</div>
					</div>

					<div class="xtop__field">
						<a class="xtop__button" href="<?php echo get_permalink(3304); ?>"><span>Каталог</span></a>

						<div class="xtop__delivery">Бесплатная доставка цветов по городу</div>

						<div class="header__search xtop__search">
							<form role="search" method="get" action="<?=get_bloginfo('url') ?>" class="search_form">
								<input type="submit" class="header__search-zoom  xtop__srch-zoom">
								<input type="text" placeholder="Поиск" name="s" id="s" class="your_latters header__search-input xtop__srch-input" required autocomplete="off">
								<input type="reset" class="header__search-close xtop__srch-close">
								<input type="hidden" value="product" name="post_type">
							</form>
							<div class="header__search_result"></div>
						</div>
					</div>

					<div class="xtop__contact">
						<div class="xtop__phone"><?
							if(count($big_data['phones']) > 1)
							{
								$teg_a = array("-", "(", ")", " ", "+");
								$teg_b = array("", "", "", "", "");

								$v_new = str_replace(
									$teg_a, $teg_b, $big_data['phones'][2]
								);

								?><a class="xtop__phone-there" href="tel:<?=$v_new ?>">
									<nobr><?=$big_data['phones'][2] ?></nobr>
								</a>

								<div class="xtop__phone-free">звонок бесплатный</div><? 
							}
						?></div><?

						if(xs_get_option('xs_is_hide_messenger') != 'on')
						{
							?><div class="xtop__soc xtop-soc"><?
								
								if(!empty(xs_get_option('xs_social_link_wa')))
								{
									?><a class="xtop-soc__item xtop-soc__item--whatsapp" href="https://wa.clck.bar/<?=xs_get_option('xs_social_link_wa') ?>?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5.%20%D0%A3%20%D0%BC%D0%B5%D0%BD%D1%8F%20%D0%B5%D1%81%D1%82%D1%8C%20%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81:" target="_blank"></a><?
								}
								
								if(!empty(xs_get_option('xs_social_link_teleg')))
								{
									?><a class="xtop-soc__item xtop-soc__item--tg" href="https://t.me/<?=xs_get_option('xs_social_link_teleg')?>" target="_blank"></a><?
								}
								
								if(!empty(xs_get_option('xs_social_link_max')))
								{
									?><a class="xtop-soc__item xtop-soc__item--max" href="<?=xs_get_option('xs_social_link_max') ?>" target="_blank"></a><?
								}
								
							?></div><?
						}
					?></div>

					<div class="xtop__basket">
						<div class="header__basket" data-load="xs_cart">
							<? get_template_part('load/xs_cart_new');?>
						</div>
					</div>

					<div class="xtop__mobile contact-mobile">
						<div class="contact-mobile__phone"><?
							if(count($big_data['phones']) > 1)
							{
								$teg_a = array("-", "(", ")", " ", "+");
								$teg_b = array("", "", "", "", "");

								$v_new = str_replace(
									$teg_a, $teg_b, $big_data['phones'][2]
								);

								?><a href="tel:<?=$v_new ?>"><?=$big_data['phones'][2] ?></a>
								<span>Звонок бесплатный</span><? 
							}
						?></div>
						<div class="contact-mobile__soc xtop-soc"><?
						
							if(!empty(xs_get_option('xs_social_link_wa')))
							{
								?><a class="xtop-soc__item xtop-soc__item--whatsapp" href="https://wa.clck.bar/<?=xs_get_option('xs_social_link_wa') ?>?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5.%20%D0%A3%20%D0%BC%D0%B5%D0%BD%D1%8F%20%D0%B5%D1%81%D1%82%D1%8C%20%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81:" target="_blank"></a><?
							}
							
							if(!empty(xs_get_option('xs_social_link_teleg')))
							{
								?><a class="xtop-soc__item xtop-soc__item--tg" href="https://t.me/<?=xs_get_option('xs_social_link_teleg')?>" target="_blank"></a><?
							}
							
							if(!empty(xs_get_option('xs_social_link_max')))
							{
								?><a class="xtop-soc__item xtop-soc__item--max" href="<?=xs_get_option('xs_social_link_max')?>" target="_blank"></a><?
							}
							
						?></div>
					</div>
				</div>

				<?/*<div class="header__top">
					<a class="logo" href="<?=esc_url(home_url( '/' )) ?>">
						<!-- <span class="logo__text">Бесплатная доставка по городу</span> -->
						<picture>
							<source type="image/webp" srcset="<?php bloginfo('template_url'); ?>/images/pics/logo.webp">
							<img src="<?php bloginfo('template_url'); ?>/images/pics/logo.jpg" alt="Логотип Цветы.ру — доставка цветов в Великом Новгороде" width="255" height="109">
						</picture>
					</a>
					<div class="header__fusion">
						<div class="header__phones"><?
							if(count($big_data['phones']) > 1)
							{
								$teg_a = array("-", "(", ")", " ", "+");
								$teg_b = array("", "", "", "", "");

								$v_new = str_replace(
									$teg_a, $teg_b, $big_data['phones'][2]
								);

								?><a class="header__phone header__phone--top" href="tel:<?=$v_new ?>">
									<nobr><?=$big_data['phones'][2] ?></nobr>
								</a><?
							}
							?><div class="header__wr-phone">
								<div class="header__phone-txt">нажми для звонка</div><?
								if(count($big_data['phones']) > 1)
								{
									$teg_a = array("-", "(", ")", " ", "+");
									$teg_b = array("", "", "", "", "");

									$v_new = str_replace(
										$teg_a, $teg_b, $big_data['phones'][1]
									);

									?><a class="header__phone" href="tel:<?=$v_new ?>"><nobr><?=$big_data['phones'][1] ?></nobr></a><?
								}
							?></div>
						</div>
						<div class="header__anycalls"><?
						
							if(count($big_data['phones']) > 1)
							{
								$teg_a = array("-", "(", ")", " ", "+");
								$teg_b = array("", "", "", "", "");

								$v_new = str_replace(
									$teg_a, $teg_b, $big_data['phones'][2]
								);
								?><a class="header__freecall" href="tel:<?=$v_new ?>" data-button="Отправить">звонок бесплатный</a><?
							}
							
							?><div class="header__colibr"><?
							
								if(xs_get_option('xs_is_hide_messenger') != 'on')
								{
									?><div class="messengers header__messengers"><?
										if(!empty(xs_get_option('xs_social_link_wa')))
										{
											?><a class="messenger-icon header__messenger-icon messenger-whatsapp" href="https://wa.clck.bar/<?=xs_get_option('xs_social_link_wa') ?>?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5.%20%D0%A3%20%D0%BC%D0%B5%D0%BD%D1%8F%20%D0%B5%D1%81%D1%82%D1%8C%20%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81:" target="_blank">
												<svg class="icon-whatsapp">
													<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-whatsapp">
												</svg>
											</a><?
										}

										if(!empty(xs_get_option('xs_social_link_teleg')))
										{
											?><a class="messenger-icon header__messenger-icon messenger-telegram" href="https://t.me/<?=xs_get_option('xs_social_link_teleg')?>" target="_blank">
												<svg class="icon-telegram">
													<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-telegram">
												</svg>
											</a><?
										}
									?></div><?
								}
								
								?><div class="header__basket" data-load="xs_cart"><?
									
									get_template_part('load/xs_cart');
									
								?></div><?
								
								?><!--iframe class="header__iframe-yandex" src="https://yandex.ru/sprav/widget/rating-badge/112831692676" width="150" height="50" frameborder="0"></iframe-->
							</div>
						</div>
					</div>
					<div class="header__exlude"><?
					
						if(xs_get_option('xs_recall_show') == 'on')
						{
							?><a class="header__exlude-call btn btn-green fancybox" href="#xs_recall" data-theme="Заказ обратного звонка" data-yandexid="zozhead" data-button="Заказать звонок">
								Заказать звонок
							</a><?
						}
						
						?><div class="header__search">
							<form role="search" method="get" action="<?=get_bloginfo('url') ?>" class="search_form">
								<input type="submit" class="header__search-zoom">
								<input type="text" placeholder="поиск" name="s" id="s" class="your_latters header__search-input" required autocomplete="off">
								<input type="reset"class="header__search-close">
								<input type="hidden" value="product" name="post_type">
							</form>
							<div class="header__search_result"></div>
						</div>
					</div>
				</div>*/?>
				<div class="header__menu">
					<nav>
						<div class="buttonMenu"><span></span></div>
						<div class="menu_container">
							<span class="close"></span>
							<div class="menu_wrapper">
								<div class="menu_wrapper_inner">
									<div class="menu__tabs_btn">
										<div class="menu__tabs_btn-tab active" onclick="jQuery('.menu__tabs-tab, .menu__tabs_btn-tab').removeClass('active');jQuery(this).addClass('active');jQuery('.menu__tabs-tab[data-tab=catalog]').addClass('active');">Каталог</div>
										<div class="menu__tabs_btn-tab" onclick="jQuery('.menu__tabs-tab, .menu__tabs_btn-tab').removeClass('active');jQuery(this).addClass('active');jQuery('.menu__tabs-tab[data-tab=menu]').addClass('active');">Меню</div>
									</div>
									<div class="menu__tabs">
										<div class="menu__tabs-tab active" data-tab="catalog"><?
									
											wp_nav_menu(array(
												'theme_location' => $big_data['device']['is_mobile'] ? 'header-menu-mobile' : 'header-menu',
												'items_wrap'     => '<ul>%3$s</ul>',
												'container'	  	 => false,
												'item_spacing'	 => 'discard'
											));
											
											if(xs_get_option('xs_is_hide_messenger') != 'on')
											{
												?><div class="messengers header__messengers"><?
												
													if(!empty(xs_get_option('xs_social_link_wa')))
													{
														?><a class="messenger-icon header__messenger-icon messenger-whatsapp" href="https://wa.clck.bar/<?=xs_get_option('xs_social_link_wa') ?>?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5.%20%D0%A3%20%D0%BC%D0%B5%D0%BD%D1%8F%20%D0%B5%D1%81%D1%82%D1%8C%20%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81:" target="_blank">
															<svg class="icon-whatsapp">
																<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-whatsapp">
															</svg>
														</a><?
													}

													if(!empty(xs_get_option('xs_social_link_teleg')))
													{
														?><a class="messenger-icon header__messenger-icon messenger-telegram" href="https://t.me/<?=xs_get_option('xs_social_link_teleg')?>" target="_blank">
															<svg class="icon-telegram">
																<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-telegram">
															</svg>
														</a><?
													}

													if(!empty(xs_get_option('xs_social_link_max')))
													{
														?><a class="messenger-icon header__messenger-icon messenger-max" href="<?=xs_get_option('xs_social_link_max')?>" target="_blank">
															<img class="icon-max" src="<?php bloginfo('template_url'); ?>/images/icons/icon-max.svg" alt="MAX" width="37" height="37" loading="lazy">
														</a><?
													}
													
												?></div><?
											}
											
										?></div>
										<div class="menu__tabs-tab" data-tab="menu"><?
									
											wp_nav_menu(array(
												'theme_location' => 'menu-zakaz-i-oplata',
												'items_wrap'     => '<ul>%3$s</ul>',
												'container'	  	 => false,
												'item_spacing'	 => 'discard'
											));

											wp_nav_menu(array(
												'theme_location' => 'menu-dostavka-i-oplata',
												'items_wrap'     => '<ul>%3$s</ul>',
												'container'	  	 => false,
												'item_spacing'	 => 'discard'
											));
											
											wp_nav_menu(array(
												'theme_location' => 'menu-nashi-kontakti',
												'items_wrap'     => '<ul>%3$s</ul>',
												'container'	  	 => false,
												'item_spacing'	 => 'discard'
											));
										
										?></div>
									</div>
								</div>
							</div>
						</div>
					</nav>
				</div>
			</div>
		</div>				

				</header>
	<!-- Хедер END --><?
	
	if(!is_front_page())
	{
		?><div class="xs_content_container"><?
			?><div class="container"><?

			/*if(!is_404())*/
			woocommerce_breadcrumb();			
	}