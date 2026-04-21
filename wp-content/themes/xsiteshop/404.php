<? 
get_header(); 

?><div class="page_404"><?
	?><a class="link_404" href="<?=get_bloginfo("url") ?>"><?
		?><img data-src="<?=get_bloginfo('template_directory') ?>/images/404.png" alt="404" /><?
	?></a><?
	?><p>Извините, но данная страница не найдена.</p><?
	?><a class="btn" href="<?=get_bloginfo("url") ?>">Перейти на главную</a><?
	
?></div><?

get_footer();
