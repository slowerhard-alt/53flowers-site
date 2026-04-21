<?

$banner_1 = wp_get_attachment_url(xs_get_option('xs_banner_1'));
$banner_1_link = xs_get_option('xs_banner_1_link');
$banner_1_blank = xs_get_option('xs_banner_1_blank');

$banner_2 = wp_get_attachment_url(xs_get_option('xs_banner_2'));
$banner_2_link = xs_get_option('xs_banner_2_link');
$banner_2_blank = xs_get_option('xs_banner_2_blank');

if($banner_1 || $banner_2 || is_super_admin())
{
	?><div class="wr_sold_out"><?
		?><div class="container"><?
			?><div class="sold_out xs_flex"><?
			
				?><div class="item eye<?=($banner_1_link && !empty($banner_1_link)) ? " link" : "" ?>"><?
				
					if(is_super_admin())
					{
						?><a target="_blank" href="/wp-admin/admin.php?page=xs_setting&tab=banners#id_Широкий баннер под сезонной распродажей" class="xs_link_edit"></a><?
					}
				
					if($banner_1)
					{
						if($banner_1_link && !empty($banner_1_link))
						{
							?><a href="<?=$banner_1_link ?>"<?=$banner_1_blank == 'on' ? ' target="_blank"' : '' ?> class="image<?=$banner_1_link == '#xs_recall' ? " fancybox" : "" ?>"<?=$banner_1_link == '#xs_recall' ? ' data-theme="Заказ обратного звонка" data-button="Заказать звонок"' : "" ?>><?
								?><img src="<?=xs_img_resize($banner_1, 1050, 240) ?>" alt="" /><?
							?></a><?
						}
						else
						{
							?><span class="image"><?
								?><img src="<?=xs_img_resize($banner_1, 1050, 240) ?>" alt="" /><?
							?></span><?
						}
					}
				
				?></div><?
				
				?><div class="item additional<?=($banner_2_link && !empty($banner_2_link)) ? " link" : "" ?>"><?
				
					if(is_super_admin())
					{
						?><a target="_blank" href="/wp-admin/admin.php?page=xs_setting&tab=banners#id_Узкий баннер под сезонной распродажей" class="xs_link_edit"></a><?
					}
				
					if($banner_2)
					{
						if($banner_2_link && !empty($banner_2_link))
						{
							?><a href="<?=$banner_2_link ?>"<?=$banner_2_blank == 'on' ? ' target="_blank"' : '' ?> class="image<?=$banner_2_link == '#xs_recall' ? " fancybox" : "" ?>"<?=$banner_2_link == '#xs_recall' ? ' data-theme="Заказ обратного звонка" data-button="Заказать звонок"' : "" ?>><?
								?><img src="<?=xs_img_resize($banner_2, 330, 240) ?>" alt="" /><?
							?></a><?
						}
						else
						{
							?><span class="image"><?
								?><img src="<?=xs_img_resize($banner_2, 330, 240) ?>" alt="" /><?
							?></span><?
						}
					}
				
				?></div><?
				
			?></div><?
		?></div><?
	?></div><?
}