<?
if ( ! defined( 'ABSPATH' ) ) exit;

global $product; 

?><li><?
	?><a href="<?=esc_url(get_permalink($product->id)) ?>" title="<?=esc_attr($product->get_title()); ?>"><?
	
		$src = wp_get_attachment_image_src($product->image_id, 'shop_catalog');
		$src = xs_img_resize($src[0], 48, 48);
		
		?><span class="image"><?
			?><img data-lazy="<? echo $src  ?>" data-src="<? echo $src  ?>" alt="" /><?
		?></span><?
		
		?><span class="product-title"><?=$product->get_title(); ?></span><?
		?><span class="xs_prices"><?
			echo $product->get_price_html(); 
		?></span><?
		
	?></a><?
?></li><?