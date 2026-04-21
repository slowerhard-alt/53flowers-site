<?
global $wpdb;

// Регистрируем тип записей "Слайдер"

function xs_salesline_register() {
 
	$labels = array(
		'name' => __('Все акции'),
		'singular_name' => __('Акция'),
		'add_new' => __('Добавить акцию'),
		'add_new_item' => __('Добавить новую акцию'),
		'edit_item' => __('Изменить акцию'),
		'new_item' => __('Новая акция'),
		'view_item' => __('Посмотреть акцию'),
		'search_items' => __('Искать акцию'),
		'not_found' =>  __('Здесь пока пусто...'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => '',
		'menu_name' => 'Акции',
	);
 
	$args = array(
		'labels' => $labels,
		'public' => false,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-format-gallery',
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 57,
		'supports' => array('title'),
		'register_meta_box_cb' => 'xs_add_meta'
	); 
		 
	register_post_type( 'salesline' , $args );
}


add_action("admin_init", "xs_add_meta_salesline", 0);

function xs_add_meta_salesline()
{
	add_meta_box("xs_credits_sales_meta", "Настройки", "xs_credits_sales_meta", "salesline", "normal", "low");   

}


function xs_credits_sales_meta( $post ) 
{
	wp_nonce_field( 'xs_meta_box_nonce', 'meta_box_nonce' ); 
	$xs_options = get_post_meta($post->ID, 'xs_options', true);
	
	?><p><label>
		Ссылка:<br/>
		<input type="text" style="width: 100%;" name="xs_link" value="<?=$xs_options['link'] ?>" />
	</label></p>
	<p><label>
		<input type="checkbox" name="xs_target" value="y" <? if ($xs_options['target'] == 'y') echo "checked" ?> />
		Открывать в новой вкладке?
	</label>
	</p>
	<p><label>
		<input type="checkbox" name="xs_show_more" value="y" <? if ($xs_options['show_more'] == 'y') echo "checked" ?> />
		Показать кнопку "Подробнее"?
	</label>
	</p><p><label>
		Текст для кнопки "Подробнее":<br/>
		<input type="text" style="width: 100%;" name="xs_more_text" value="<?=$xs_options['more_text'] ?>" />
	</label></p><?
	
	/*
	<p><label>
		Выберите цвет:<br/>
		<input type="color" name="xs_color" value="<?=$xs_options['color'] ?>" style="margin-top: 5px;width: 90px;"/>
	</label></p><?
	*/
}


// Сохраняем параметры


function xs_save_details_sale($post_id)
{
	global $wpdb;

	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'xs_meta_box_nonce' ) || !current_user_can( 'edit_post', $post_id ) || (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)) 
		return; 

	foreach($_POST as $k => $v)
	{
		if(strpos($k, 'xs_') !== false && !empty($v))
			$result[str_replace('xs_', '', $k)] = xs_format($v);
	}
	
	if(isset($result) && !empty($result))
		update_post_meta($post_id, 'xs_options', $result);
	else
		delete_post_meta($post_id, 'xs_options');	
	
	return $xsdata;  
}

add_action('init', 'xs_salesline_register');
add_action('save_post', 'xs_save_details_sale');

?>