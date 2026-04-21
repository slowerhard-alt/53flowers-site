<script> xs_active_menu("admin.php?page=calculate") </script>

<h1 class="wp-heading-inline">Калькулятор букета</h1>
<br/><br/>
<div class="tab_container" data-product_id='<?=$xs_data['product_id'] ?>'><?
	?><div class="xs_load_ajax" data-product_id='<?=$xs_data['product_id'] ?>'><?
	
		get_ajax_template('xs_product_components', $xs_data);
		
	?></div><?
?></div>

<br/><br/>
<div class="button calculate_button--copy">Скопировать в буфер</div>