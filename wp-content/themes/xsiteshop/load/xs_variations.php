<?
global $big_data;

$big_data['not_cache'] = 'y';

include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

$product_id = (int)xs_format($_GET['product_id']);

if($product_id == 0 || !$product = wc_get_product($product_id))
{
	header("HTTP/1.0 404 Not Found");
	die(':(');
}

?><div class="modal_variation"><?
	
	if($product->product_type == "variable")
	{
		?><div class="modal_variation__title">Выберите размер букета</div><?
		
		?><form class="cart xs_add_to_cart_form" method="post" enctype="multipart/form-data"><?
		
			do_action('xs_template_init');
			
			$available_variations = $product->get_available_variations();
			$i = 0;
			
			?><div class="modal_variation__container<?=get_post_meta($product_id, '_attr_one_column', true) == 'y' ? " modal_variation__container--one_column" : ""?>"><?
			
			foreach($available_variations as $k => $v)
			{
				$variation_id = $v['variation_id'];
				$variable_product = new WC_Product_Variation($variation_id);
				
				/*
				if($price_html = $variable_product->get_price_html()) 
				{
					?><div class="modal_variation__item"><?
						?><input id="radio_<?=$i ?>" type="radio" required name="add-to-cart" value="<?=$variation_id ?>" >
						<label for="radio_<?=$i ?>">
							<?=$big_data['sizes'][$v['attributes']['attribute_pa_kolichestvo']]->name ?> - <nobr><?=$price_html ?></nobr>
						</label><?
					?></div><?
					
					$i++;
				}
				*/
				
				if($price = $variable_product->get_price()) 
				{
					foreach($v['attributes'] as $_v)
					{
						$attribute_name = $_v;
						break;
					}
					
					$is_disabled = get_post_meta($variation_id, 'is_disabled', true) == 'yes';
					
					?><div class="modal_variation__item<?=$is_disabled ? " is_disabled" : "" ?>"><?
						?><input id="radio_<?=$i ?>" type="radio" required name="add-to-cart" value="<?=$variation_id ?>"<?=$is_disabled ? " disabled" : "" ?>>
						<label for="radio_<?=$i ?>"<?=$is_disabled ? ' title="Временно не доступен для покупки"' : "" ?>>
							<?=$big_data['sizes'][$attribute_name]->name ?> - <nobr><?=wc_price($price) ?></nobr>
						</label><?
					?></div><?
					
					$i++;
				}
			}
			
			?></div><?
			
			?><button type="submit" name="add-to-cart" value="<?=esc_attr($product->get_id()) ?>" class="modal_variation__btn btn squeeze__listing-btn squeeze__order">Добавить в корзину</button><?
			?><input type="hidden" name="quantity" value="1" /><?
			
		?></form><?
	}
	else
	{
		
	}
	
?></div><?