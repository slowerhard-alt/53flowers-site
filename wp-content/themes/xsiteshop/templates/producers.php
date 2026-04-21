<?

if(xs_get_option('xs_shop_show_producers'))
{
	$producers = get_terms('producer', array(
		'hide_empty'    => false, 
		'parent'         => '0',
		'hierarchical'  => true, 
		'child_of'      => 0, 
		'cache_domain'  => 'core',
	));

	if(count($producers) > 0 && xs_get_option('xs_shop_show_brands'))
	{
		?><div class="wr_brands"><?
			?><div class="container"><?
				?><div class="brands xs_flex<?=xs_get_option('xs_shop_brands_arrows') ? ' show_arrows' : '' ?>"><?
					
					foreach($producers as $producer)
					{
						if($img = wp_get_attachment_url(get_woocommerce_term_meta($producer->term_id, '_thumbnail_id')))
						{
							$src = xs_img_resize($img, 100, 57, 'contain');
							
							?><div class="item"><? 
								?><a href="<?=get_term_link($producer->term_id, "producer") ?>" class="xs_flex xs_middle"><?
									?><img data-src="<? echo $src  ?>" data-lazy="<? echo $src ?>" title="<?=$producer->name ?>" alt="<?=$producer->name ?>" /><?
								?></a><?
							?></div><?
						}
					}
					
				?></div><?
			?></div><?
		?></div><?
		
		?><script type="text/javascript"><?
			
			?>jQuery(function($)<?
			?>{<?
				?>$('.wr_brands .brands').slick({<?
					?>slidesToShow:6,<?
					?>slidesToScroll:1,<?
					?>arrows:<?=xs_get_option('xs_shop_brands_arrows') ? 'true' : 'false' ?>,<?
					?>autoplay:<?=xs_get_option('xs_shop_brands_drag') ? 'true' : 'false' ?>,<?
					?>autoplaySpeed:<?=xs_get_option('xs_shop_brands_scrollamount') ?>,<?
					?>infinite:true,<?
					?>dots:false,<?
					?>lazyLoad:'ondemand',<?
					?>responsive:[<?
					?>{<?
						?>breakpoint:1200,<?
						?>settings:{<?
							?>slidesToShow:5<?
						?>}<?
					?>},<?
					?>{<?
						?>breakpoint:1000,<?
						?>settings:{<?
							?>slidesToShow:4<?
						?>}<?
					?>},<?
					?>{<?
						?>breakpoint:850,<?
						?>settings:{<?
							?>slidesToShow:3<?
						?>}<?
					?>},<?
					?>{<?
						?>breakpoint:550,<?
						?>settings:{<?
							?>slidesToShow:2,<?
							?>slidesToScroll:2<?
						?>}<?
					?>},<?
					?>{<?
						?>breakpoint:355,<?
						?>settings:{<?
							?>slidesToShow:1,<?
							?>slidesToScroll:1<?
						?>}<?
					?>}]<?
				?>})<?
			?>})<?
			
		?></script><?
	}
}