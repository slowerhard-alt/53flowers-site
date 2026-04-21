<? 
get_header(); 

$slider = get_posts(
	array(
		'post_type'       => 'slider',
		'post_status'     => 'publish',
		'numberposts'	  => -1
	) 
);

if(count($slider) > 0)
{
	?><div class="main-slider__inner" <?
		?>data-autoplay="<?=xs_get_option('xs_autoslider') ? 1 : 0 ?>" <?
		?>data-stophover="<?=xs_get_option('xs_slider_pause_on_hover') ? 1 : 0 ?>" <?
		?>data-speed="<?=xs_get_option('xs_slider_speed') ?>" <?
		?>data-autoplay_speed="<?=xs_get_option('xs_slider_timeout') ?>" <?
		?>data-effect="<?=xs_get_option('xs_slider_effect') ? 1 : 0 ?>" <?
		?>data-arrows="<?=xs_get_option('xs_slider_show_arrow') ? 1 : 0 ?>" <?
		?>data-dots="<?=xs_get_option('xs_slider_show_button') ? 1 : 0 ?>" <?
		
		$style = array();
		
		if(xs_get_option('xs_slider_width') > 0)
		{
			$style[] = "max-width:".xs_get_option('xs_slider_width')."px";
			$width = xs_get_option('xs_slider_width');
		}
		else 
			$width = 1422;
		
		$height = xs_get_option('xs_slider_height') > 0 ? xs_get_option('xs_slider_height') : 398;
		$style[] = "max-height:".$height."px";
		
		if(count($style) > 0)
		{
			?>style="<?=implode(';',$style)?>" <?
		}
		
	?>><? 
		
		$i = 0;
		
		foreach ( $slider as $v ) 
		{
			$url = wp_get_attachment_url( get_post_thumbnail_id($v->ID) );
			
			if($url)
			{
				$xs_options = get_post_meta($v->ID, 'xs_options', true);

				?><div class="main-slider__item"><?
					if(is_super_admin())
					{
						?><a target="_blank" href="/wp-admin/post.php?post=<?=$v->ID ?>&action=edit" class="xs_link_edit"></a><?
					}
					?><img class="main-slider__item-img" src="<? echo xs_img_resize($url, $width, $height) ?>" alt="<?=esc_attr($v->post_title ?: "Баннер Цветы.ру")?>" width="<?=$width ?>" height="<?=$height ?>">
					<div class="main-slider__text main-slider__text-list main-slider__<?=$xs_options['position'] ?>"><?
						if(!empty($v->post_excerpt) || !empty($v->post_content))
						{
							if(!empty($v->post_content))
							{
								if(!empty($v->post_excerpt))
								{
								?><div class="main-slider__text-tlt">
									<span><?=$v->post_excerpt; ?></span>
								</div><?
								}
								echo $v->post_content;

								if(!empty($xs_options['link']) && $xs_options['show_more'] == 'y')
								{
									?><div class="main-slider__text-go">
										<a class="main-slider__text-link btn" href="<?=$xs_options['link']?>" <? if($xs_options['target'] == 'y') echo 'target="_blank" ' ?>><?=isset($xs_options['more_text']) && !empty($xs_options['more_text']) ? $xs_options['more_text'] : "Перейти" ?></a>
									</div><?
								}

							}
						}
					?></div>
				</div><?
			}
			
			$i++;
		}
		
	?></div><?
}