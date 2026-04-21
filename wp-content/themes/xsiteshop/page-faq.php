<? 
get_header(); 
		
while ( have_posts() ) : the_post(); 
	?><h1><? the_title(); ?></h1><?
endwhile;

if(is_super_admin())
{
	?><a target="_blank" href="/wp-admin/edit.php?post_type=faq" class="xs_link_edit"></a><?
}

$faq = new WP_Query(array(
	'post_type'       => 'faq',
	'post_status'     => 'publish'
));


if(count($faq->posts) > 0)
{ 
	$i = 0;
	
	?><div class="faq"><?
		
		foreach($faq->posts as $v)
		{
			$i++;
			?><div class="item"><?
				?><div class="question"><?
					?><a href="#<?=$v->post_name ?>" onclick="jQuery(this).parents('.item').toggleClass('active'); return false" class="name xs_flex xs_start"><?
						?><span class="number"><?=str_pad($i, 2, '0', STR_PAD_LEFT) ?></span><?
						?><span class="label"><?=$v->post_title ?></span><?
					?></a><?
				?></div><?
				?><div class="answer" id="<?=$v->post_name ?>"><?

					if(is_super_admin())
					{
						?><a target="_blank" href="/wp-admin/post.php?post=<?=$v->ID ?>&action=edit" class="xs_link_edit"></a><?
					}
				
					echo wpautop($v->post_content);
					
				?></div><?
			?></div><?
		}
		
	?></div><?
}

?><p>Если у вас остались вопросы, вы можете позвонить нам или <a href="#xs_recall" class="fancybox" rel="nofollow" data-theme="Заказ обратного звонка" data-button="Заказать звонок">заказать обратный звонок</a>.</p><?


get_footer();

