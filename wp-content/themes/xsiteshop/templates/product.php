<?
if(!defined('ABSPATH')) exit;

global $product, $big_data;

$ar_prices = get_product_prices($product);

?><div class="product"><?
	?><div class="product__wrap"><?
	
		if(is_super_admin())
		{
			?><a target="_blank" href="/wp-admin/post.php?post=<?=$product->id ?>&action=edit" class="xs_link_edit"></a><?
			?><a target="_blank" href="/wp-content/themes/xsiteshop/load/xs_set_tags.php?product_id=<?=$product->id ?>&d=<?=strtotime('now') ?>" class="xs_link_edit_tags fancybox-labels" data-type="ajax"></a><?
			?><a target="_blank" href="/wp-content/themes/xsiteshop/load/xs_set_top.php?product_id=<?=$product->id ?>&d=<?=strtotime('now') ?>" class="xs_link_edit_top fancybox-labels" data-type="ajax"></a><?
		}

		?><a class="product__image goods__content-image" href="<?=get_permalink($product->id) ?>">
			<span class="product__image-inn"><?
			
				if($ar_prices['percent'])
				{
					?><span class="sale_percent"><?=$ar_prices['percent'] ?></span><?
				}
		
				$product->post_title = $product->name;
				$product->ID = $product->id;
				echo get_copy_links_html($product);
				
				if($labels = get_the_terms($product->id, 'label')) 
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
		
				if($big_data['device']['is_mobile'])
					$width_img = 280;
				else
					$width_img = 250;

				$src = wp_get_attachment_image_src($product->image_id, 'full');
				$src = xs_img_resize($src[0], $width_img, $width_img, 'contain');

				$sizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$src);
				
				?><img class="product__img goods__content-img" src="<? echo $src ?>" alt="<?=htmlspecialchars($product->name) ?>" width="<?=isset($sizes[0]) ? $sizes[0] : $width_img ?>" height="<?=isset($sizes[1]) ? $sizes[1] : $width_img ?>" loading="lazy"><? 
			?></span><?
			
			if(xs_get_option('xs_is_fresh_delivery') == 'on')
			{
				if(get_post_meta($product->id, '_fresh_delivery', true) == 'y')
				{
					?><span class="product__fresh_delivery"><?
						?><span class="product__fresh_delivery-label">Свежая поставка</span><?
					?></span><?
				}
			}
			
		?></a>
		<a class="product__title goods__content-title" href="<?=get_permalink($product->id) ?>">
			<span><?
			
				$name = get_post_meta($product->id, '_title_list', true);
				
				if($name && !empty($name))
					echo str_replace("|", "<br/>", $name);
				else
					echo $product->name;			
			
			?></span>
		</a>
		<div class="product__price goods__content-prices"><?
		
			if ( $price_html = $product->get_price_html() ) 
				echo $price_html;
			
		?></div>
		<div class="product__paycontainer"><?
			
			if($product->is_in_stock())
			{
				?><a class="product__pay btn" href="<?=get_permalink($product->id) ?>"><?=$big_data['list_product_btn'] ?></a><?
			}
			else
				echo "нет в наличии";
			
		?></div>
	</div>
</div><?
