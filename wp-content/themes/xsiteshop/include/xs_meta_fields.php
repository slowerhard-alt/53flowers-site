<? 
function xs_meta_data_title( $post )
{
	global $wpdb, $xs_global;
	wp_nonce_field( 'xs_meta_box_nonce', 'meta_box_nonce' ); 
	
	?><br/>
	<div class="pd-switch__item">
		<input name="_attr_one_column" id="_attr_one_column" type="checkbox" <?=get_post_meta($post->ID, '_attr_one_column', true) == 'y' ? ' checked' : '' ?> value="y">
		<label for="_attr_one_column">Показывать атрибуты в одну колонку</label>
	</div><?
}

function xs_meta_data_switch( $post )
{
	global $wpdb, $xs_global;
	wp_nonce_field( 'xs_meta_box_nonce', 'meta_box_nonce' ); 

	$xs_block_active = get_post_meta($post->ID, 'xs_block_active', true);
	
	if(!is_array($xs_block_active))
		$xs_block_active = [];
	
	?>

	<div class="pd-switch">
		<div class="pd-switch__item">
			<input name="xs_block_active[decor]" id="xs_decor" type="checkbox" <?=$xs_block_active['decor'] == 'y' ? ' checked' : '' ?> value="y">
			<label for="xs_decor">Отключить блок Оформления</label>
		</div>
		<div class="pd-switch__item">
			<input name="xs_block_active[addition]" id="xs_addition" type="checkbox" <?=$xs_block_active['addition'] == 'y' ? ' checked' : '' ?> value="y">
			<label for="xs_addition">Отключить блок Добавить к заказу</label>
		</div>
		<div class="pd-switch__item">
			<input name="xs_block_active[adding]" id="xs_prod_adding" type="checkbox" <?=$xs_block_active['adding'] == 'y' ? ' checked' : '' ?> value="y">
			<label for="xs_prod_adding">Отключить блок Так же добавить</label>
		</div>
		<div class="pd-switch__item">
			<input name="xs_block_active[similar]" id="xs_prod_similar" type="checkbox" <?=$xs_block_active['similar'] == 'y' ? ' checked' : '' ?> value="y">
			<label for="xs_prod_similar">Отключить блок Похожие товары</label>
		</div>

		<div class="pd-switch__item">
			<input name="xs_block_active[gift]" id="xs_prod_gift" type="checkbox" <?=$xs_block_active['gift'] == 'y' ? ' checked' : '' ?> value="y">
			<label for="xs_prod_gift">Отключить блок Заказ в подарок</label>
		</div>
	</div><?
}

function xs_meta_data_tabs( $post )
{
	global $wpdb, $xs_global;

	$ar_tabs = get_post_meta($post->ID, '_tabs', true);
	
	$db_tab = get_posts([
		'post_type' => 'tabs'
	]);
	
	?><div class="pd-switch"><?
	
		foreach($db_tab as $v)
		{
			?><div class="pd-switch__item">
				<input name="_tabs[<?=$v->ID ?>]" id="xs_tab_<?=$v->ID ?>" type="checkbox" <?=isset($ar_tabs[$v->ID]) && $ar_tabs[$v->ID] == 'y' ? ' checked' : '' ?> value="y">
				<label for="xs_tab_<?=$v->ID ?>"><?=$v->post_title ?></label>
			</div><?
		}
		
	?></div><?
}

function xs_add_meta()
{
	add_meta_box("xs_meta_data_title", "Дополнительные опции", "xs_meta_data_title", "product", "normal", "high");
	add_meta_box("xs_meta_data_switch", "Отображение блоков", "xs_meta_data_switch", "product", "side", "low");
	//add_meta_box("xs_meta_data_tabs", "Табы", "xs_meta_data_tabs", "product", "side", "low");
}

add_action('edit_form_after_title', function() 
{
    global $post, $wp_meta_boxes;
	
	if($post->post_type != "product")
		return;
	
	?><div class="xs_product-title">
		<div class="xs_product-label">Заголовок в списке (для переноса строки символ "|")</div>
		<input type="text" name="_title_list" value="<?=get_post_meta($post->ID, '_title_list', true) ?>" style="width: 100%;">
	</div>
	<div class="xs_product-title">
		<div class="xs_product-label">Загаловок у доп описания</div>
		<input type="text" name="xs_product-title" value="<?=get_post_meta($post->ID, 'xs_product-title', true) ?>" style="width: 100%;">
	</div><?
});

add_action("admin_init", "xs_add_meta");


function xs_save_meta($post_id)
{
    global $wpdb;

	$post = get_post($post_id);
	
    if(isset($_POST['xs_product-title']))
        update_post_meta($post_id, 'xs_product-title', $_POST['xs_product-title']);

    if(isset($_POST['_title_list']))
        update_post_meta($post_id, '_title_list', $_POST['_title_list']);

    if(isset($_POST['_attr_one_column']))
        update_post_meta($post_id, '_attr_one_column', $_POST['_attr_one_column']);
    else
    	delete_post_meta($post_id, '_attr_one_column');

    if(isset($_POST['_size_sale']))
        update_post_meta($post_id, '_size_sale', $_POST['_size_sale']);


    if(isset($_POST['xs_block_active']))
        update_post_meta($post_id, 'xs_block_active', $_POST['xs_block_active']);
    else
    	delete_post_meta($post_id, 'xs_block_active');

    if(isset($_POST['_tabs']))
        update_post_meta($post_id, '_tabs', $_POST['_tabs']);
    else
    	delete_post_meta($post_id, '_tabs');


	if($post->post_type == 'product')
	{
		if(isset($_POST['_regular_price']) && isset($_POST['_sale_price']))
		{
			if(isset($_POST['_regular_price']) && isset($_POST['_sale_price']) && !empty($_POST['_regular_price']) && !empty($_POST['_sale_price']))
				update_post_meta($post_id, '_size_sale', ($_POST['_regular_price']-$_POST['_sale_price']));
			else
				update_post_meta($post_id, '_size_sale', 0);
		}
		else
		{
			$regular_price = get_post_meta($post_id, '_regular_price', true);
			$sale_price = get_post_meta($post_id, '_sale_price', true);
			
			if($regular_price && !empty($regular_price) && $sale_price && !empty($sale_price))
				update_post_meta($post_id, '_size_sale', ($regular_price-$sale_price));
			else
				update_post_meta($post_id, '_size_sale', 0);
		}
	}
}

add_action('save_post', 'xs_save_meta'); 


// Задаём размер скидки всем товарам

//add_action('xs_template_init', 'xs_update_sale_size');

function xs_update_sale_size()
{
	$arg = [
	    'limit'  => -1, // All products
		'status' => 'publish',
	];
	
	$products = wc_get_products($arg);
	
	foreach($products as $v)
	{
		$regular_price = $v->regular_price;
		$sale_price = $v->sale_price;
		
		if($regular_price && !empty($regular_price) && $sale_price && !empty($sale_price))
			update_post_meta($v->get_id(), '_size_sale', ($regular_price-$sale_price));
		else
			update_post_meta($v->get_id(), '_size_sale', 0);
	}
	

	pre($products);
	die();
}
?>