<? 
header("Cache-Control: no-store, no-cache, must-revalidate");

get_header(); 
	
while(have_posts()) : the_post();
	
	?><h1><? the_title() ?></h1><?	
	
	if(xs_get_option('xs_shop_show_favorit') == true)
	{
		if(count($big_data['compare']) > 0)
		{
			$products = new WP_Query(array(
				"numberposts" => -1,
				"post_status" => "publish",
				"post__in" => $big_data['compare'],
				"post_type" => "product"
			));
		}
		
		if(isset($products) && $products->have_posts())
		{
			?><div class="compare_shadow"><?
			
				?><div class="compare_container"><?

					$is_article = false;
					$is_tags = false;
					
					while($products->have_posts())
					{
						$products->the_post();
						
						foreach($product->attributes as $k => $v)
						{
							if(!isset($attributes[$v['name']]))
							{
								if(mb_substr($v['name'], 0, 3, "utf-8") == "pa_" && $tax = get_taxonomy($v['name']))
								{
									$attributes[$k]['name'] = $tax->labels->name_admin_bar;
									
									$terms = get_terms(array(
										"taxonomy" => $v['name'],
										"hide_empty" => false,
									));
									
									foreach($terms as $_v)
										$attributes[$k]['values'][$_v->term_id] = $_v->name;
								}
								else
									$attributes[$k]['name'] = $v['name'];
							}
						}
						
						if(wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable')))
							$is_article = true;
						
						if(!empty($product->get_tags()))
							$is_tags = true;
					}
					
					
					?><table class="compare_table"><?
						?><tbody><?
							
							// Фото
							
							?><tr><?
								?><td></td><?
							
								while($products->have_posts())
								{
									$products->the_post();
									
									?><td><?
										?><div class="compare_image"><?
											
											?><a href="#" rel="nofollow" data-product_id="<?=$product->id ?>" class="xs_change_compare xs_middle xs_flex active"><span class="text">Убрать из сравнения</span><span class="close"></span></a><?
										
											?><a href="<? the_permalink() ?>" class="img" style="<? 
						
											if(xs_get_option('xs_shop_photo_position') != 'cover')
											{ 
												?>height:<?=xs_get_option('xs_shop_image_height_section') ?>px<? 
											}
											else
											{
												$p = xs_get_option('xs_shop_image_height_section') / xs_get_option('xs_shop_image_width_section') * 100;
												?>padding-top:<?=$p ?>%<?
											}
											?>"><?
											
												if($labels = get_the_terms($product->id, 'label')) 
												{	
													?><span class="xs_labels"><?
													
														foreach($labels as $l)
														{
															?><span class="label"<?=isset($big_data['label_colors'][$l->term_id]['xs_label_color']) ? ' style="color:'.$big_data['label_colors'][$l->term_id]['xs_label_color'].'"' : '' ?>><?
																?><span class="bg"<?=isset($big_data['label_colors'][$l->term_id]['xs_label_bg']) ? ' style="background:'.$big_data['label_colors'][$l->term_id]['xs_label_bg'].'"' : '' ?>></span><?
																?><span class="text"><?=$l->name ?></span><?
															?></span><?
														}
														
													?></span><?
												}	
												
												$src = wp_get_attachment_image_src($product->image_id, 'full' );
												$src = xs_img_resize($src[0], xs_get_option('xs_shop_image_width_section'), xs_get_option('xs_shop_image_height_section'), xs_get_option('xs_shop_photo_position'));
												
												?><span class="image image2 xs_flex xs_center xs_middle"><?
													?><img data-lazy="<? echo $src ?>" data-src="<? echo $src  ?>" alt="<?=$product->name ?>" /><?
												?></span><?
												
											?></a><?
										?></div><?
									?></td><?
								}
								
							?></tr><?
							
							
							// Название
							
							?><tr><?
								?><td>Наименование товара</td><?
								
								while($products->have_posts())
								{
									$products->the_post();
									
									?><td><a href="<?=get_permalink($product->ID) ?>"><?=$product->name ?></a></td><?
								}
								
							?></tr><?
							
							
							// Цена
							
							?><tr><?
								?><td>Цена</td><?
								
								while($products->have_posts())
								{
									$products->the_post();
									
									?><td><?								
										?><div class="xs_prices"><? 
			
											if($price_html = $product->get_price_html()) 
												echo $price_html;
											
										?></div><?
									?></td><?
								}
								
							?></tr><?
							
							
							// Наличие
							
							if(xs_get_option('xs_shop_show_stock_list'))
							{
								?><tr><?
									?><td>Наличие</td><?
									
									while($products->have_posts())
									{
										$products->the_post();
										
										?><td><?								
											?><div class="stock<?=$product->is_in_stock() ? '' : ' empty' ?>"><?
												echo $product->is_in_stock() ? 'В наличии' : 'Нет в наличии';
											?></div><?
										?></td><?
									}
									
								?></tr><?
							}
							
							
							// Артикул
							
							if($is_article)
							{
								?><tr><?
									?><td>Артикул</td><?
									
									while($products->have_posts())
									{
										$products->the_post();
										
										?><td><?
										
											if($product->get_sku() || $product->is_type('variable'))
											{
											?><div class="sku"><?
												echo ($sku = $product->get_sku()) ? $sku : "-";
											?></div><?
											}
											
										?></td><?
									}
									
								?></tr><?
							}
							
						
							// Атрибуты
							
							foreach($attributes as $k => $v)
							{
								?><tr><?
									?><td><?=$v['name'] ?></td><?
									
									while($products->have_posts())
									{
										$products->the_post();
										
										?><td><?
										
											if(isset($product->attributes[$k]))
											{
												if(is_array($product->attributes[$k]['options']))
												{
													if(count($product->attributes[$k]['options'] > 0))
													{
														$values = array();
														
														foreach($product->attributes[$k]['options'] as $_v)
															$values[] = isset($v['values'][$_v]) ? $v['values'][$_v] : $_v;
														
														echo implode(", ", $values);
													}
												}
												else
													echo $product->attributes[$k]['options'];
											}
											
										?></td><?
									}
									
								?></tr><?
							}
							
							
							// Производитель
							
							if(xs_get_option('xs_shop_show_producers'))
							{
								?><tr><?
									?><td>Производитель</td><?
									
									while($products->have_posts())
									{
										$products->the_post();
										
										?><td><?
										
											$producers = get_the_terms( $post->ID, 'producer');
											
											if(count($producers) > 0)
											{
												$p = array();
												
												foreach($producers as $v)
													$p[] = '<a href="'.get_category_link($v->term_id).'">'.$v->name.'</a>';
													
												echo implode(', ', $p);
											}
											
										?></td><?
									}
									
								?></tr><?
							}
							
							
							// Категории
							
							?><tr><?
								?><td>Категории</td><?
								
								while($products->have_posts())
								{
									$products->the_post();
									
									?><td><?
									
										echo $product->get_categories();
										
									?></td><?
								}
								
							?></tr><?
							
							
							// Метки
							
							if($is_tags)
							{
								?><tr><?
									?><td>Метки</td><?
									
									while($products->have_posts())
									{
										$products->the_post();
										
										?><td><?
										
											echo $product->get_tags();
											
										?></td><?
									}
									
								?></tr><?
							}
							
							
							// В корзину
							
							if($product->is_in_stock()) 
							{
								?><tr><?
									?><td></td><?
									
									while($products->have_posts())
									{
										$products->the_post();
										
										?><td><?
										
											if($product->product_type == "variable")
											{
												?><a href="<?=get_permalink() ?>" class="btn">Выбрать комплект</a><?
											}
											else
											{
												?><form class="cart xs_add_to_cart_form" method="post" enctype="multipart/form-data"><?
													?><div class="button_pay_container xs_flex xs_start xs_middle"><?
														?><div class="xs_flex xs_count_container"><?
															?><div class="count"><?
															
																?><input type="text" name="quantity" class="input-text qty text" value="<?=isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : $product->get_min_purchase_quantity() ?>" /><?
																
															?></div><?
															?><div class="buttons"><?
																?><span class="plus"></span><?
																?><span class="minus"></span><?
															?></div><?
														?></div><?

														?><div class="button_pay xs_flex xs_middle"><?

															if($product->is_in_stock()) 
															{
																?><button type="submit" name="add-to-cart" value="<?=esc_attr($product->get_id()) ?>" class="btn buy<?=$product->is_purchasable() ? ' add_to_cart_button' : '' ?>">В корзину</button><?
															}
																	
														?></div><?
													?></div><?
													?><input type="hidden" name="add-to-cart" value="<? echo esc_attr($product->get_id()) ?>" /><?
												?></form><?
											}
											
										?></td><?
									}
									
								?></tr><?
							}
							
						?></tbody><?
					?></table><?
				?></div><?
				?><div class="shadow"></div><?
			?></div><?
		}
		else
		{
			?><div class="empty-box">
				<div class="look">
					<div class="image compare"></div>
					<div class="text">
						<p>Вы еще не добавили товары в сравнение.</p>
					</div>
				</div>
				<p class="return-to-shop"><?
					?><a href="<?=get_permalink(3304) ?>" class="btn">Перейти в каталог</a><?	
				?></p>
			</div><?
		}
	}
	else
		the_content();
	
endwhile;

get_footer();