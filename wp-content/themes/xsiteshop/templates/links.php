<? 
global $big_data;

if(!$big_data['device']['is_mobile'])
	return "";

$links = get_posts(
	array(
		'post_type'       => 'menu',
		'post_status'     => 'publish',
		'numberposts'	  => -1
	) 
);
	
if(count($links) > 0)
{
	?><section class="showup showup--tune">
		<div class="container">
			<div class="showup__inner">
				<div class="showup__slider"><? 
			
					foreach($links as $v) 
					{

						$url = wp_get_attachment_url(get_post_thumbnail_id($v->ID));
						
						if($url)
						{
							$xs_options = get_post_meta($v->ID, 'xs_options', true);

							?><div class="showup__item"><?
							
								if(isset($xs_options['link']) && !empty($xs_options['link']))
								{
									?><a class="showup__link" href="<?=$xs_options['link'] ?>" <? if($xs_options['target'] == 'y') echo 'target="_blank" ' ?>><?
								}
								else
								{
									?><span class="showup__link"><?
								}
								
									?><div class="showup__image">
										<picture>
											<img class="showup__img" src="<?=(mb_strpos($url, '.svg', 0, 'utf-8') === false)
												? xs_img_resize($url, 87, 87)
												: $url;
											
											?>" width="48" height="48" loading="lazy" alt="<?=htmlspecialchars($v->post_title) ?>" >
										</picture>
									</div>
									<div class="showup__text"><?=$v->post_title ?></div><?
							
								if(isset($xs_options['link']) && !empty($xs_options['link']))
								{
									?></a><?
								}
								else
								{
									?></span><?
								}
								
							?></div><?
						}
					}
			
				?></div>
			</div>
		</div>
	</section><?
}