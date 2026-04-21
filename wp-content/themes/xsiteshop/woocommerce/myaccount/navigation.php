<?
if(!defined('ABSPATH')) exit;

do_action('woocommerce_before_account_navigation');

?><div class="widget-area"><?
	?><div class="change_trigger xs_flex"><?
		?><span class="item catalog">Навигация</span><?
	?></div><? 
	?><div class="widget woocommerce widget_product_categories"><?
		?><ul class="product-categories"><?
	
			foreach(wc_get_account_menu_items() as $endpoint => $label)
			{
				if(strpos(esc_url(wc_get_account_endpoint_url($endpoint)), 'downloads') === false)
				{ 
					?><li class="<?=str_replace("is-active", "current-cat", wc_get_account_menu_item_classes($endpoint)) ?>"><?
						?><a href="<?=esc_url(wc_get_account_endpoint_url($endpoint)) ?>"><? 
						
							echo str_replace(
								array('Адреса', 'Заказы', 'Консоль'),
								array('Адрес доставки', 'Мои заказы', 'Кабинет'),
								esc_html($label)
							); 
							
						?></a><?
					?></li><?
				}			
			}
			
		?></ul><?
	?></div><?
?></div><?

do_action('woocommerce_after_account_navigation');
