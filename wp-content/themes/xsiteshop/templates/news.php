<?

if(xs_get_option('xs_news') && xs_get_option('xs_last_news'))
{
	global $new;

	$arg = array(
		'post_type'       => 'post',
		'post_status'     => 'publish',
		'orderby' 		  => ['post_date' => 'DESC'],
		'posts_per_page'  => 8
	);
		
	$news = new WP_Query($arg);

	if(count($news->posts) > 0)
	{
	?><div class="wr_news"><?
		?><div class="container"><?
			?><div class="news xs_flex"><?

				?><div class="knowledge"><?
				
					if(is_super_admin())
					{
						?><a target="_blank" href="/wp-admin/admin.php?page=xs_setting&tab=main#id_Новости" class="xs_link_edit"></a><?
					}
					
					?><div class="title"><?=xs_get_option('xs_last_news_title') ?></div><?
					?><div class="txt"><?=xs_get_option('xs_last_news_description') ?></div><?
					?><div class="push"><?
						?><a href="<?=get_permalink(3337) ?>" class="btn shadow-hover"><?=xs_get_option('xs_last_news_btn') ?></a><?
						?><div class="links xs_flex"><?
							?><span class="arrows xput arrows-left" onclick="jQuery('.wr_news .news .focus').slick('slickPrev')"></span><?
							?><span class="arrows xput arrows-right" onclick="jQuery('.wr_news .news .focus').slick('slickNext')"></span><?
						?></div><?
					?></div><?
				?></div><?

				?><div class="focus slider xs_flex"><?

					foreach($news->posts as $new)
						get_template_part('templates/new');
					
				?></div><?
			?></div><?
		?></div><?
	?></div><?
	}
}