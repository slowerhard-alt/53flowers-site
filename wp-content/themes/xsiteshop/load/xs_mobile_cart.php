<?

global $big_data;

if(empty($big_data) || count($big_data) == 0)
{
	$big_data['not_cache'] = 'y';
	
	include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
	do_action( 'xs_template_init' );
}

if(xs_get_option('xs_shop_show_favorit') == true)
{
	?><a href="<?=get_permalink(2559) ?>" rel="nofollow" class="compare common"><?
		?><span class="much"><?=count($big_data['compare']) ?></span><?
	?></a><?
}

?><a href="<?=$big_data['cart']['url'] ?>" rel="nofollow" class="basket xs_flex xs_middle"><?
	?><span class="basket-icon common"><?
		?><span class="much"><?=$big_data['cart']['count'] ?></span><?
	?></span><?
?></a><?
