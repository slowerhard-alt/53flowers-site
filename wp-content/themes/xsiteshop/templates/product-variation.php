<?

if(!defined('ABSPATH')) exit;

global $product, $big_data;

$ar_prices = get_product_prices($product);

?><div class="squeeze__item"><?

	if(is_super_admin())
	{
		?><a target="_blank" href="/wp-admin/post.php?post=<?=$product->id ?>&action=edit" class="xs_link_edit"></a><?
		?><a target="_blank" href="/wp-content/themes/xsiteshop/load/xs_set_tags.php?product_id=<?=$product->id ?>&d=<?=strtotime('now') ?>" class="xs_link_edit_tags fancybox-labels" data-type="ajax"></a><? 
		?><a target="_blank" href="/wp-content/themes/xsiteshop/load/xs_set_top.php?product_id=<?=$product->id ?>&d=<?=strtotime('now') ?>" class="xs_link_edit_top fancybox-labels" data-type="ajax"></a><? 
	}

	$ar_images = [];
	
	if($big_data['device']['is_mobile'])
		$width_img = 472;
	else
		$width_img = 277;

	if($src = wp_get_attachment_image_src($product->image_id, 'full'))
		$ar_images[] = xs_img_resize($src[0], $width_img, $width_img);


	$gallery = $product->get_gallery_attachment_ids();
	
	if($gallery)
		foreach($gallery as $v)
			if($src = wp_get_attachment_image_src($v, 'full'))
				$ar_images[] = xs_img_resize($src[0], $width_img, $width_img);
		
		
	$labels = get_the_terms($product->id, 'label')
	
	?><div class="squeeze__wr-slider"><?
		
		$product->post_title = $product->name;
		$product->ID = $product->id;
		echo get_copy_links_html($product);
	
		if($ar_prices['percent'])
		{
			?><span class="sale_percent"><?=$ar_prices['percent'] ?></span><?
		}
			
		if(count($ar_images) > 0)
		{
			?><div class="squeeze__count">
				<span class="squeeze__current-numb">1</span>
				<span class="squeeze__nav-slash">/</span>
				<span><?=count($ar_images) ?></span>
			</div><?
		}	

		if($labels) 
		{
			?><span class="xlabel"><?
				foreach($labels as $l)
				{
					?><span class="xlabel__mark" <?=isset($big_data['label_colors'][$l->term_id]['xs_label_bg']) ? ' style="background-color:'.$big_data['label_colors'][$l->term_id]['xs_label_bg'].'"' : '' ?>>
						<?=$l->name ?>
					</span><?
				}
			?></span><?
		}

		if(xs_get_option('xs_is_fresh_delivery') == 'on')
		{
			if(get_post_meta($product->id, '_fresh_delivery', true) == 'y')
			{
				?><span class="product__fresh_delivery"><?
					?><span class="product__fresh_delivery-label">Свежая поставка</span><?
				?></span><?
			}
		}
		
		?><div class="squeeze__slider"><?
		
			if(count($ar_images) > 0)
			{
				foreach($ar_images as $v)
				{
					?><a class="squeeze__image" href="<?=get_permalink($product->id) ?>"><?
						?><img class="squeeze__img" src="<?=$v ?>" width="<?=$width_img ?>" height="<?=$width_img ?>" loading="lazy"><?
					?></a><?
				}
			}

		?></div>
	</div>
	<div class="squeeze__listing">
		<a href="<?=get_permalink($product->id) ?>" class="squeeze__listing-ahead"><span><?
		
			$name = get_post_meta($product->id, '_title_list', true);
			
			if($name && !empty($name))
				echo str_replace("|", "<br/>", $name);
			else
				echo $product->name;
		
		?></span></a>
		<div class="squeeze__listing-body"><?
			
			if($product->product_type == "variable")
			{
				$is_more = false;
				$available_variations = $product->get_available_variations();
				
				$i = 0;
				
				foreach($available_variations as $k => $v)
				{
					$variation_id = $v['variation_id'];
					
					if(get_post_meta($variation_id, 'is_hide', true) == 'yes' || get_post_meta($variation_id, '_stock_status', true) == 'outofstock')
						continue;
					
					$i++;
					
					if($i > $big_data['product_variation_count'])
					{
						$is_more = true;
						//break;
					}
					
					$variable_product = new WC_Product_Variation($variation_id);
					
					/*
					if($price_html = $variable_product->get_price_html()) 
					{
						?><div class="squeeze__listing-item"><?=$big_data['sizes'][$v['attributes']['attribute_pa_kolichestvo']]->name ?> - <?=$price_html ?></div><?
					}
					*/
					
					foreach($v['attributes'] as $_v)
					{
						$attr_code = $_v;
						break;
					}
						
					if($price = $variable_product->get_price()) 
					{
						$regular_price = (int)$variable_product->get_regular_price();
						
						$percent = $regular_price > $price
							? (($p = round(100 - ($price / $regular_price * 100))) >= 5 ? "-".$p."%" : 0)
							: 0;	
							
						?><div class="squeeze__listing-item<?=($i > $big_data['product_variation_count']) ? " squeeze__listing-item--hide" : "" ?>">
							<?=$big_data['sizes'][$attr_code]->name ?> - <?=wc_price($price) ?><?
						
							if($percent)
							{
								?><span class="modal_variation__sale_percent"><?=$percent ?></span><?
							}
						
						?></div><?
					}
				}
				
				if($is_more)
				{
					?><div class="squeeze__listing-item-more">Показать ещё</div><?
				}
			}
			
		?></div>	
		<div class="squeeze__listing-buttons">
			<div class="squeeze__listing-buttons">
				<div class="wr-squeeze__listing-btn">
					<a href="<?=get_permalink($product->id) ?>" class="btn squeeze__listing-btn squeeze__more"><?=$big_data['list_product_top_btn'] ?></a>
				</div><?
				
				/*
				if($product->is_in_stock())
				{
					if($product->product_type == "variable")
					{
						?><div class="btn squeeze__listing-btn squeeze__order xs_get_variation" data-product_id="<?=esc_attr($product->get_id()) ?>">Оформить заказ</div><?
					}
					else
					{
						?><form class="cart xs_add_to_cart_form" method="post" enctype="multipart/form-data"><?
							?><button type="submit" name="add-to-cart" value="<?=esc_attr($product->get_id()) ?>" class="btn squeeze__listing-btn squeeze__order">Оформить заказ</button><?
							?><input type="hidden" name="add-to-cart" value="<? echo esc_attr($product->get_id()) ?>" /><?
							?><input type="hidden" name="quantity" value="1" /><?
						?></form><?
					}
				}
				else
					echo "нет в наличии";
				*/
				
			?></div>
		</div>
	</div>	
</div>
