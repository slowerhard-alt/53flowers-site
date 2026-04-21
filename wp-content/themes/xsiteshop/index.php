<? get_header(); 
	
while(have_posts()) : the_post();
	?><div class="page_content"><?
	
		?><div class="catalog_head"><?
			?><h1><?=get_the_title(get_option('page_for_posts')) ?></h1><?
		?></div><?
		
		if(is_super_admin())
		{
			?><a target="_blank" href="/wp-admin/post.php?post=<?=$post->ID ?>&action=edit" class="xs_link_edit"></a><?
		}
	
		the_content();
		
	?></div><?
endwhile;

get_footer();