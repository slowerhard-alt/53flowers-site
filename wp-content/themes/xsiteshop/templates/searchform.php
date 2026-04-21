<form role="search" method="get" action="<?=get_bloginfo('url') ?>" class="form_search"><?
	?><div class="own xs_flex xs_middle"><?
		?><input type="text" autocomplete="off" value="<?=get_search_query() ?>" name="s" class="your_latters input_search" placeholder="Поиск"><?
		?><input type="hidden" value="product" name="post_type"><?
		?><input type="submit" class="zoom"><?
	?></div><?
	?><div class="close_it"><?
		?><input type="reset"><?
	?></div><?
?></form><?