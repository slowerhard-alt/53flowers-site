<?

global $big_data;

if(empty($big_data) || count($big_data) == 0)
{
	$big_data['not_cache'] = 'y';
	
	include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
	do_action( 'xs_template_init' );
}

?><a href="<?=$big_data['cart']['url'] ?>" rel="nofollow" class="header__basket-link"><?
	?><span class="header__basket-icon"><?
		?><span class="header__basket-count"><?=$big_data['cart']['count'] ?></span><?
	?></span><?
?></a><?								
