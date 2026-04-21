<? 
if(isset($big_data['categories_hide_product']) && count($big_data['categories_hide_product']))
{
	if(has_term($big_data['categories_hide_product'], 'product_cat', $post))
	{
		global $post, $page, $wp_query;
		$wp_query->set_404();
		header("HTTP/1.1 404 Not Found");
		get_template_part('404');
		die();
	}
}

get_header();

if ( ! defined( 'ABSPATH' ) ) exit;

if ( post_password_required() ) 
{
	echo get_the_password_form();
	return;
}

if(is_super_admin())
{
	?><a target="_blank" href="/wp-admin/post.php?post=<?=$product->id ?>&action=edit" class="xs_link_edit"></a><?
	?><a target="_blank" href="/wp-content/themes/xsiteshop/load/xs_set_tags.php?product_id=<?=$product->id ?>&d=<?=strtotime('now') ?>" class="xs_link_edit_tags fancybox-labels" data-type="ajax"></a><?
	?><a target="_blank" href="/wp-content/themes/xsiteshop/load/xs_set_top.php?product_id=<?=$product->id ?>&d=<?=strtotime('now') ?>" class="xs_link_edit_top fancybox-labels" data-type="ajax"></a><?
}

$ar_prices = get_product_prices($product);

$big_data['block_active'] = get_post_meta($post->ID, 'xs_block_active', true);

?>
	<!-- Карточка товара START -->
	<div class="p-product p-product--tune">
		<div class="p-product__inner">
			<div class="p-product__body">
				<div class="p-product__bodyin">
					<div class="p-product__images">
						<div class="p-product__imagesslider"><?

							$attachment_ids = $product->get_gallery_attachment_ids();
							$images = array_merge(array($product->image_id), $attachment_ids);

							if (count($images) > 0) 
							{
								if($big_data['device']['is_mobile'])
									$width_img = 560;
								else
									$width_img = 560;

								foreach($images as $v)
								{
									$src = wp_get_attachment_image_src($v, 'full');
									$props = wc_get_product_attachment_props($v, $post);
									$src = xs_img_resize($src[0], $width_img, $width_img, 'contain');

									?><div class="p-product__images-inn" data-image_id="<?=$v ?>"><?
	
										if($ar_prices['percent'] && !empty($ar_prices['percent']))
										{
											?><span class="sale_percent"><?=$ar_prices['percent'] ?></span><?
										}

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

										if(xs_get_option('xs_is_fresh_delivery') == 'on')
										{
											if(get_post_meta($product->id, '_fresh_delivery', true) == 'y')
											{
												?><span class="product__fresh_delivery product__fresh_delivery--detail"><?
													?><span class="product__fresh_delivery-label">Свежая поставка</span><?
												?></span><?
											}
										}

										?><div class="p-product__image">
											<img class="p-product__img" src="<? echo $src ?>" width=<?=$width_img ?> height=<?=$width_img ?> alt="<?=!empty($props['alt']) ? $props['alt'] : $product->name ?>">
										</div>
									</div><?
								}
							}
							
						?></div><?
						
						
						// Миниатюры
						
						if(count($images) > 1)
						{
							?><div class="p-product__thunbslider"><?

							foreach($images as $v)
							{
								$src = wp_get_attachment_image_src($v, 'full' );
								$props = wc_get_product_attachment_props($v, $post);
								
								?><div class="slide"><?
									?><div class="image_container"><?
										?><span class="image xs_flex xs_center xs_middle"><?
											
											$src = xs_img_resize($src[0], 96, 96, xs_get_option('xs_shop_photo_position_detail'));
											
											?><img src="<? echo $src ?>" width="96" height="96" alt="<?=!empty($props['alt']) ? $props['alt'] : $product->name ?>"<?//=!empty($props['title']) ? ' title="'.$props['title'].'"' : '' ?>><?
										?></span><?
									?></div><?
								?></div><?
							}
							
							?></div><?
						}
						
						?><div class="p-product__lead--mobile">
							<div class="p-product__aname">
								<div class="p-product__title"><? the_title() ?></div>
							</div><?
							
							?><div class="p-product__lead"><?
								get_template_part('templates/product_pay');
							?></div><?

							get_template_part('templates/soc-order');?>
						</div>
					
						<div class="p-product__bodyout"><?
							
							get_template_part('templates/tabs');
							
						?></div><?
					
					?></div>
					<div class="p-product__descript">
						<div class=" p-product__lead--pc">
							<div class="p-product__aname">
								<h1 itemprop="name"><? the_title() ?></h1>
							</div><?
							
							?><div class="p-product__lead"><?
								get_template_part('templates/product_pay');
							?></div><?

							get_template_part('templates/soc-order'); ?>
						</div>

						<div class="p-product__learn"><?

							if($org = get_post_meta($post->ID, 'xs_product-title', true)) {
								?><div class="p-product__learn-tlt">
									<span><?=$org ?></span>
								</div><?
							}

							?><div class="p-product__learn-info">
								<div class="p-product__learn-txt">
									<?=wpautop($product->short_description)?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><?
	
	get_template_part('templates/gift');
	get_template_part('templates/related');
	

get_footer(); 

