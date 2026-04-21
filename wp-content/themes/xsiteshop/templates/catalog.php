<?
global $big_data;

if(is_super_admin())
{
	?><a target="_blank" href="/wp-admin/admin.php?page=xs_setting&tab=shop#id_Главная страница каталога" class="xs_link_edit"></a><?
}

if(count($big_data['categories']) > 0)
{
	?><div class="main_catalog xs_flex xs_start xs_wrap"><?
	
	foreach($big_data['categories'] as $v)
	{
		if($v->parent == 0)
		{
			if(!$src = wp_get_attachment_image_src(get_woocommerce_term_meta($v->term_id, 'thumbnail_id', true), "shop_catalog"))
				$src = get_bloginfo('template_url')."/images/noimage_news.gif";
			else
				$src = $src[0];
	
			$src = xs_img_resize($src, 330, 216);
				
			?><div class="item"><?
			
				?><div class="cont"><?

					if(is_super_admin())
					{
						?><a target="_blank" href="/wp-admin/term.php?taxonomy=<?=$v->taxonomy ?>&tag_ID=<?=$v->term_id ?>&post_type=product" class="xs_link_edit"></a><?
					}
					
					?><a href="<?=get_term_link($v->term_id, 'product_cat') ?>"><?
						?><span class="image xs_inline xs_flex xs_center xs_middle"><?
							?><img data-src="<? echo $src ?>" alt="" /><?
							
							if(xs_get_option("xs_shop_catalog_show_count"))
							{
								?><span class="count"><?=$v->count ?> <?=format_by_count($v->count, "товар", "товара", "товаров") ?></span><?
							}
							
						?></span><?
						
						?><span class="name"><?=$v->name ?></span><?
					?></a><?
					
					$subcat = array();
					
					foreach($big_data['categories'] as $_v)
						if($_v->parent == $v->term_id)
							$subcat[] = $_v;
					
					$i = 1;
					if(count($subcat) > 0 && xs_get_option("xs_shop_catalog_count_subcat") > 0)
					{
						?><div class="subcat"><?
							?><ul><?
							
							foreach($subcat as $_v) 
							{ 
								if($i > xs_get_option("xs_shop_catalog_count_subcat"))
									break;
								
								?><li><a href="<?=get_term_link($_v->term_id, 'product_cat') ?>"><?=$_v->name ?></a></li><?
								$i++;
							} 
						
							?></ul><?
						?></div><? 

						if(xs_get_option("xs_shop_catalog_show_all"))
						{
							?><div class="more"><?
								?><a rel="nofollow" href="<?=get_term_link($v->term_id, 'product_cat'); ?>"><?=xs_get_option("xs_shop_catalog_text_all") ?> ›</a><?
							?></div><?
						}
					}
					
				?></div><?
			?></div><?
		}
	}
	
	?></div><?
}
