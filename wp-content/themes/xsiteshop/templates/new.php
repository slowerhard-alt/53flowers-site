<?
global $new;

?><div class="item"><?

	if(is_super_admin())
	{
		?><a target="_blank" href="/wp-admin/post.php?post=<?=$new->ID ?>&action=edit" class="xs_link_edit"></a><?
	}
	
	?><a href="<?=get_permalink($new->ID) ?>"><?
	
		if($src = wp_get_attachment_image_src(get_post_thumbnail_id($new->ID), 'full'))
			$src = $src[0];
		else
			$src = get_bloginfo('template_url')."/images/noimage_news.gif";
		
		?><span class="image xs_inline xs_flex xs_center xs_middle"><?
			?><img data-src="<? echo xs_img_resize($src, 330, 216) ?>" alt="" /><?
		?></span><?

		?><span class="predat"><?
			?><span class="date"><?=mb_strtolower(xs_date($new->post_date), 'utf-8') ?></span><?
			?><strong><?=$new->post_title ?></strong><?
		?></span><?
	?></a><?
?></div><?