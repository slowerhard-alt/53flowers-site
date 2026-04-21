<? 
get_header(); 

while ( have_posts() ) : the_post();
			
	if(is_super_admin())
	{
		?><a target="_blank" href="/wp-admin/post.php?post=<?=$post->ID ?>&action=edit" class="xs_link_edit"></a><?
	}

	?><div class="catalog_head"><?
		?><h1><? the_title() ?></h1><?	
	?></div><?

	?><div class="info_panel"><?
		?><div class="date">Опубликовано: <span><?=mb_strtolower(xs_date($post->post_date, true, true), 'utf-8') ?></span></div><?
	?></div><?

	?><div class="wr_minder"><?
		?><div class="minder xs_flex"><?
				
			?><div class="text"><?
				
				the_content();
				
				?><div class="clear"></div><?
				
				if(xs_get_option('xs_social_in_articles'))
				{
					?><br/><?
					get_template_part('templates/share');
				}
				
				?><br/><?
				?><a href="<?=get_permalink(3337) ?>">← Все новости</a><?
				
			?></div><?
			
			?><div class="subscribe"><?
				
				get_template_part('templates/subscribe');

			?></div><?

		?></div><?
	?></div><?

endwhile; 

get_footer();


