<?
$ar_sort = xs_woocommerce_catalog_orderby('');

if(isset($_GET['orderby']) && isset($ar_sort[$_GET['orderby']]))
	$orderby = $_GET['orderby'];

?><form class="form_sorting" method="get" action="">
	<div class="sort sort--tune">
		<div class="select">
			<div class="select_field"><?=isset($ar_sort[$orderby]) ? $ar_sort[$orderby] : "Цена (низкая — высокая)" ?></div>
			<div class="select_fields"><?
			
				foreach($ar_sort as $k => $v)
				{
					?><div onclick="jQuery(this).parents('.select_fields').next('input').val('<?=$k ?>'); jQuery(this).parents('form').submit() " data-value="<?=$k ?>" class="field<?=$orderby == $k ? " selected" : "" ?>"><?=$v ?></div><?
				}
			
			?></div>
			<input type="hidden" class="value_field" value="<?=$orderby ?>" name="orderby">
		</div>
	</div><?
	
	foreach($_GET as $k => $v)
	{
		if($k != 'orderby')
		{
			?><input type="hidden" value="<?=xs_format($v) ?>" name="<?=xs_format($k) ?>"><?
		}
	}
	
?></form><?