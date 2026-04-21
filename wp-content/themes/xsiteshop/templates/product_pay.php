<?
if(!defined('ABSPATH')) exit;

global $product, $big_data;

$_product = $product;

?><form class="cart xs_add_to_cart_form<?=$product->product_type == "variable" ? ' variations_form' : ''?>" method="post" enctype="multipart/form-data"<? 
				
	if($product->product_type == "variable") 
	{ 
		$default = '';
		$variation_attributes = $product->get_variation_attributes();
		$available_variations = $product->get_available_variations();
				
		foreach($variation_attributes as $attribute_name => $v)
			if($ar_default_attributes = get_post_meta($product->get_id(), '_default_attributes', true))
				if(is_array($ar_default_attributes) && isset($ar_default_attributes[$attribute_name]))
					$default = ['attribute_'.$attribute_name => $ar_default_attributes[$attribute_name]];
		
		$_available_variations = [];
		
		foreach($available_variations as $k => $v)
		{
			$variable_product = new WC_Product_Variation($v['variation_id']);
			
			if($v['price'] = (int)$variable_product->get_price()) 
			{
				if(get_post_meta($v['variation_id'], '_stock_status', true) == 'outofstock')
					continue;

				$v['is_default'] = $default == $v['attributes'];
				
				$v['is_disabled'] = get_post_meta($v['variation_id'], 'is_disabled', true) == 'yes';
				
				$v['regular_price'] = (int)$variable_product->get_regular_price();
				
				$v['percent'] = $v['regular_price'] > $v['price']
					? (($p = round(100 - ($v['price'] / $v['regular_price'] * 100))) >= 5 ? "-".$p."%" : 0)
					: 0;					
					
				$v['image_id'] = $variable_product->image_id;
					
				$v['image_src'] = "";
				
				if($src = wp_get_attachment_image_src($variable_product->image_id, 'full'))
					$v['image_src'] = xs_img_resize($src[0], 560, 560, 'contain');
				
				$v['name'] = trim(str_replace($product->get_name()." - ", "", $variable_product->get_name()));
				
				$_available_variations[] = $v;
				
				$i++;
			}
		}

		$available_variations = $_available_variations;
		
		?> data-product_variations="<?=htmlspecialchars(wp_json_encode($available_variations)) ?>"<? 
	} 
	
?>><?

	if($product->product_type == "variable")
	{
		$is_one_column = get_post_meta($post->ID, '_attr_one_column', true) == 'y';
		$i = isset($big_data['i']) ? $big_data['i'] : 0;
		$is_more = false;
		$_k = 0;
		
		// Если 2 и более вариативных атрибута
		 
		if(count($variation_attributes) > 1)
		{
			if(empty($available_variations) && false !== $available_variations)
			{
				?><p class="stock out-of-stock"><? _e('Этого товара нет в наличии.') ?></p><?
			}
			else
			{
				?><div class="variation_attributes"><? 
				
					foreach($variation_attributes as $attribute => $options)
					{
						$is_more = false;
						
						?><div class="attribute">
							<div class="modal_variation__attribute_name"><?=wc_attribute_label($attribute) ?>:</div>
							<div class="modal_variation__container<?=$is_one_column ? " modal_variation__container--one_column" : ""?>">
								<div class="value"><?
										
									$selected = isset($_REQUEST['attribute_'.sanitize_title($attribute)]) 
										? wc_clean(stripslashes(urldecode($_REQUEST['attribute_'.sanitize_title($attribute)]))) 
										: $product->get_variation_default_attribute($attribute);
									
									$name = 'attribute_'.sanitize_title($attribute);
									
									?><div id="attribute_<?=esc_attr($id) ?>" class="xs_attributes"><?

										if(!empty($options)) 
										{
											if($product && taxonomy_exists($attribute)) 
											{
												$terms = wc_get_product_terms($product->get_id(), $attribute, array('fields' => 'all'));

												foreach($terms as $term) 
												{
													if(in_array($term->slug, $options)) 
													{
														$_k++;
							
														if($_k >= 10)
															$is_more = true;
													
														?><div class="modal_variation__item<?=$is_more ? " is_more" : "" ?>">
															<input 
																id="attribute_<?=esc_attr($name)."_".esc_attr($term->slug)."_".$i ?>" 
																name="<?=esc_attr($name) ?>" 
																data-attribute_name="attribute_<?=esc_attr(sanitize_title($attribute))?>" 
																type="radio" 
																value="<?=esc_attr($term->slug) ?>" 
																<?=checked(sanitize_title($selected), $term->slug, false) ?>
															>
															<label for="attribute_<?=esc_attr($name)."_".esc_attr($term->slug)."_".$i ?>">
																<?=esc_html(apply_filters('woocommerce_variation_option_name', $term->name)) ?>
															</label>
														</div><?
														
														$i++;
													}
												}
											}
											else 
											{
												foreach($options as $option) 
												{
													$selected = sanitize_title($selected ) === $selected 
														? checked($selected, sanitize_title($option), false) 
														: selected($selected, $option, false);
													
													$_k++;
						
													if($_k >= 10)
														$is_more = true;
													
													?><div class="modal_variation__item<?=$is_more ? " is_more" : "" ?>">
														<input 
															id="attribute_<?=esc_attr($name)."_".esc_attr($option) ?>" 
															name="<?=esc_attr($name) ?>" 
															data-attribute_name="attribute_<?=esc_attr(sanitize_title($attribute)) ?>" 
															type="radio" 
															required
															value="<?=esc_attr($option) ?>" 
															<?=$selected ?>
														>
														<label for="attribute_<?=esc_attr($name)."_".esc_attr($option) ?>">
															<?=esc_html(apply_filters('woocommerce_variation_option_name', $option)) ?>
														</label>
													</div><?
													
													$i++;
												}
											}
										}

									?></div><?
								
								?></div>
							</div><?
			
							if($is_more)
							{
								?><div class="modal_variation__more">
									<span class="modal_variation__more-label">Показать ещё</span>
									<span class="modal_variation__more-icon"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 451.847 451.847" xml:space="preserve"><path d="M225.923 354.706c-8.098 0-16.195-3.092-22.369-9.263L9.27 151.157c-12.359-12.359-12.359-32.397 0-44.751 12.354-12.354 32.388-12.354 44.748 0l171.905 171.915 171.906-171.909c12.359-12.354 32.391-12.354 44.744 0 12.365 12.354 12.365 32.392 0 44.751L248.292 345.449c-6.177 6.172-14.274 9.257-22.369 9.257z"></path></svg></span>
								</div><?
							}
							
						?></div><?
					}
						
				?></div><?
			}	
		}
		
		// Если 1 вариативный атрибут
		
		else
		{	
			?><div class="attribute"><?
			
				$default = '';
				
				foreach($variation_attributes as $attribute_name => $v)
				{
					if($ar_default_attributes = get_post_meta($product->get_id(), '_default_attributes', true))
						if(is_array($ar_default_attributes) && isset($ar_default_attributes[$attribute_name]))
							$default = ['attribute_'.$attribute_name => $ar_default_attributes[$attribute_name]];
					
					?><div class="modal_variation__attribute_name"><?=wc_attribute_label($attribute_name); ?></div><?
					break;
				}
					
				?><div class="modal_variation__container<?=$is_one_column ? " modal_variation__container--one_column" : ""?>"><?
				
					foreach($available_variations as $k => $v)
					{
						$ar_atributes = array_keys($v['attributes']);
						$attribute_name = array_shift($ar_atributes);
						
						$_k++;
						
						if($_k >= 10)
							$is_more = true;
						
						?><div class="modal_variation__item<?=$v['is_disabled'] ? " is_disabled" : "" ?><?=$is_more ? " is_more" : "" ?>">
							<input 
								id="radio_<?=$i ?>" 
								type="radio" 
								data-price="<?=$v['price'] ?>" 
								data-percent="<?=$v['percent'] ?>" 
								data-regular_price="<?=$v['regular_price'] ?>" 
								required 
								name="<?=$attribute_name ?>" 
								value="<?=array_shift($v['attributes']) ?>"
								<?=$v['is_disabled'] ? " disabled" : "" ?> 
								data-variation_id="<?=$v['variation_id'] ?>" 
								data-image_id="<?=$v['image_id'] ?>" 
								data-image_src="<?=$v['image_src'] ?>" 
								data-is_default="<?=$v['is_default'] ? 'y' : '' ?>"
							>
							<label for="radio_<?=$i ?>"<?=$v['is_disabled'] ? ' title="Временно не доступен для покупки"' : "" ?>>
								<?=$v['name'] ?> - <nobr><strong><?=wc_price($v['price']) ?></strong><?=$v['percent'] ? '<span class="old_price">'.wc_price($v['regular_price']).'</span>' : "" ?></nobr>
							</label><?
								
							if($v['percent'])
							{
								?><span class="modal_variation__sale_percent_wrap">
									<span class="modal_variation__sale_percent"><?=$v['percent'] ?></span>
								</span><?
							}
							
						?></div><?
						
						$i++;
					}
				
				?></div><?
				
				if($is_more)
				{
					?><div class="modal_variation__more">
						<span class="modal_variation__more-label">Показать ещё</span>
						<span class="modal_variation__more-icon"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 451.847 451.847" xml:space="preserve"><path d="M225.923 354.706c-8.098 0-16.195-3.092-22.369-9.263L9.27 151.157c-12.359-12.359-12.359-32.397 0-44.751 12.354-12.354 32.388-12.354 44.748 0l171.905 171.915 171.906-171.909c12.359-12.354 32.391-12.354 44.744 0 12.365 12.354 12.365 32.392 0 44.751L248.292 345.449c-6.177 6.172-14.274 9.257-22.369 9.257z"></path></svg></span>
					</div><?
				}	

			?></div><?
		}
		
		$big_data['i'] =  $i;
	}
	else
	{
		$ar_prices = get_product_prices($product);
		
		?><input type="hidden" data-price="<?=$product->get_price() ?>" percent="<?=$ar_prices['percent'] ?>" data-regular_price="<?=(int)$product->get_regular_price() ?>" name="add-to-cart" value="<? echo esc_attr($product->get_id()) ?>" /><?
	} 
	

	if(is_product())
	{
		if($price_html = $product->get_price_html()) 
		{
			$price = $product->get_price();
			
			if(empty($price) || !is_numeric($price))
				$price = 0;
			
			$bonuse = round($price * 0.05);
			
			?><div class="p-product__price_container"><?
			
				?><div class="p-product__price">
					<?=$price_html ?>

					<meta itemprop="price" content="<?=esc_attr($product->get_price()) ?>" /><?
					?><meta itemprop="priceCurrency" content="<?=esc_attr(get_woocommerce_currency()) ?>" /><?
					?><link itemprop="availability" href="http://schema.org/<?=$product->is_in_stock() ? 'InStock' : 'OutOfStock' ?>" /><?
				?></div><?
				
				if($bonuse > 0)
				{
					?><div class="p-product__bonuse">
						<div class="p-product__bonuse-label">Бонусы 
							<span class="infotitle">
								<span class="infotitle__icon"></span>
								<span class="infotitle__desc">Накапливайте бонусы и используйте их для оплаты следующих покупок.
									<span class="infotitle__br"></span>
									1 бонус = 1 руб.
									<span class="infotitle__br"></span>
									<strong>Можно оплатить до 30% от суммы следующей покупки.</strong>
									<span class="infotitle__br"></span>
									<a href="<?=get_permalink(10836) ?>">Узнать больше о бонусной программе</a>
								</span>
							</span>
						</div>
						<div class="p-product__bonuse-value"><?=$bonuse ?></div>
					</div><?
				}
				
			?></div><?
		}
	}

	if(!isset($big_data['block_active']) || !is_array($big_data['block_active']) || !isset($big_data['block_active']['decor']) || $big_data['block_active']['decor'] != 'y')
	{
		$original_query = $wp_query;
		
		$arg = array( 
			'tax_query' => array(
				[
					'taxonomy' => 'product_tag',
					'field' => 'id',
					'terms' => 382, // Метка "Оформление"
				],
			), 
			'posts_per_page' => -1,
			'post_type' => 'product',
			'meta_key' => '_price',
			'orderby'  => ['meta_value_num'=>'ASC'],
		);	

		$addition_products = new WP_Query($arg);

		if($addition_products->have_posts())
		{
			?><div class="p-decoration_products"><?
				?><div class="p-decoration_products__label">Оформление:</div><?
				?><select placeholder="- выбрать -" multiple class="p-decoration_products__select selectator" name="decoration_product"><?
					
					while($addition_products->have_posts())
					{
						$addition_products->the_post();
						global $product;
						
						if($product->is_in_stock())
						{
							?><option data-price="<?=$product->get_price() ?>" data-regular_price="<?=(int)$product->get_regular_price() ?>" value="<?=$product->id ?>"><?=$product->name ?> (+<?=$product->get_price() ?> руб)</option><?
						}
					}
				
				?></select><?
				
				/*
				?><div class="p-decoration_products__select"><?
					
					$big_data['i'] = isset($big_data['i']) ? $big_data['i']++ : 0;
					
					while($addition_products->have_posts())
					{
						$addition_products->the_post();
						global $product;
						
						?><div class="p-decoration_products__wrapper"><?
							?><input data-price="<?=$product->get_price() ?>" data-regular_price="<?=(int)$product->get_regular_price() ?>" id="decoration_product_<?=$product->id."_".$big_data['i'] ?>" class="p-decoration_products__input" type="checkbox" name="decoration_product_<?=$product->id ?>" value="<?=$product->id ?>" /><?
							?><label for="decoration_product_<?=$product->id."_".$big_data['i'] ?>" class="p-decoration_products__item"><?
								?><?=$product->name 
							?></label><?
						?></div><?
						
						$big_data['i']++;
					}
				
				?></div><?
				*/
				
			?></div><?
		}
		
		$wp_query = $original_query;
		wp_reset_postdata();
	}
	
	$product = $_product;

	if(!isset($big_data['block_active']) || !is_array($big_data['block_active']) || !isset($big_data['block_active']['addition']) || $big_data['block_active']['addition'] != 'y')
	{
		$original_query = $wp_query;
		
		$arg = array( 
			'tax_query' => array(
				[
					'taxonomy' => 'product_tag',
					'field' => 'id',
					'terms' => 368, // Метка "Добавить к заказу"
				],
			), 
			'posts_per_page' => -1,
			'post_type' => 'product',
			'meta_key' => '_price',
			'orderby'  => ['meta_value_num'=>'ASC'],
		);	

		$addition_products = new WP_Query($arg);

		if($addition_products->have_posts())
		{
			?><div class="p-addition_products"><?
				?><div class="p-addition_products__label">Добавить к заказу:</div><?
				?><select placeholder="- выбрать -" multiple class="p-addition_products__select selectator" name="addition_product"><?
					
					while($addition_products->have_posts())
					{
						$addition_products->the_post();
						global $product;
						
						if($product->is_in_stock())
						{
							?><option data-price="<?=$product->get_price() ?>" data-regular_price="<?=(int)$product->get_regular_price() ?>" value="<?=$product->id ?>"><?=$product->name ?> (+<?=$product->get_price() ?> руб)</option><?
						}
					}
				
				?></select><?
			?></div><?
		}
		
		$wp_query = $original_query;
		wp_reset_postdata();
	}
	
	$product = $_product;
	
	?><div class="p-product__pay_buttons"><?
		
		if($product->is_in_stock())
		{
			?><button type="submit" class="btn p-product__order btn-green">В корзину</button><?
			
			?><input type="hidden" name="add-to-cart" value="<?=esc_attr($product->get_id()) ?>"><?

			if(xs_get_option('xs_payoneclick_show') == 'on')
			{
				?><a class="btn p-product__click fancybox" href="#xs_recall_one_click" data-button="Оформить заказ" data-theme="Заказ в один клик" data-yandexid="zakaz1click" rel="nofollow">Заказ в один клик</a><?
			}
		}
		else
			echo "нет в наличии";
		
		?></div><?
	
	?><input type="hidden" name="quantity" value="1" /><?
?></form><?
