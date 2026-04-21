<?
global $xs_data, $filter;

?><div class="title_block">
	<h1 class="wp-heading-inline">Статистика поисковых запросов</h1>
</div>

<form method="get" class="xs_filter" action="">
	<div class="dates_block"><?
		
		?><nobr>Период:
			<label class="xs_date">
				<input type="date" name="filter[date_ot]" value="<?=$filter['date_ot']?>" />
			</label> 
				- &nbsp;
			<label class="xs_date">
				<input type="date" name="filter[date_do]" value="<?=$filter['date_do']?>" />
			</label>
		</nobr><?
		
		?><label class="xs_submit">
			<input type="submit" value="Фильтровать" class="button-primary" />
		</label>
		<?
			if($setFilter)
			{
				?><label class="xs_submit"><a href="/wp-admin/admin.php?page=<?=$_GET['page']?>&tab=<?=$_GET['tab']?>&orderby=<?=$orderby?>&order=<?=$order?>">× Сбросить фильтр</a></label><?
			}
		?>
		
		<input type="hidden" name="page" value="<?=$_GET['page']?>" />
		<input type="hidden" name="tab" value="<?=$_GET['tab']?>" />
		<input type="hidden" name="orderby" value="<?=$orderby?>" />
		<input type="hidden" name="order" value="<?=$order?>" />
		<input type="hidden" name="paged" value="1" />
	</div>
</form>

<table class="wp-list-table widefat striped xs_data_table xs_users">
	<thead>
		<tr><?
		
			get_order_th('Запрос', 'query');
			get_order_th('Количество', 'quantity');
			get_order_th('Дата первого поиска', 'date');
			
		?></tr>
	</thead>
	<tbody><? 
	
	foreach($xs_data->rows as $val)
	{ 
		?><tr>
			<td><?=$val->query ?></td>
			<td><?=$val->quantity ?></td>			
			<td><?=date("d.m.Y H:i:s", strtotime($val->date)) ?></td>			
		</tr><? 
	} 
	?></tbody>
</table>