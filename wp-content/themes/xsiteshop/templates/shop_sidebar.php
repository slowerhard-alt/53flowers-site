<?
if ( ! defined( 'ABSPATH' ) ) exit;

?><div class="xs_sidebar"><? 

	if(is_active_sidebar('shop'))
	{
		?><div class="widget-area"><?
			?><div class="change_trigger xs_flex"><?
				?><span class="item catalog">Каталог</span><?
				?><span class="item filter">Фильтр</span><?
			?></div><? 
			
			dynamic_sidebar( 'shop' ); 
			
		?></div><?
	}
	
?></div><? 