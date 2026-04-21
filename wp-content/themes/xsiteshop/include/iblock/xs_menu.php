<?
global $wpdb;

// Регистрируем тип записей "Ссылки"

function xs_menu_register() {
 
	$labels = array(
		'name' => __('Все ссылки'),
		'singular_name' => __('Ссылки'),
		'add_new' => __('Добавить ссылку'),
		'add_new_item' => __('Добавить ссылку'),
		'edit_item' => __('Изменить ссылку'),
		'new_item' => __('Новая ссылка'),
		'view_item' => __('Посмотреть ссылку'),
		'search_items' => __('Искать ссылку'),
		'not_found' =>  __('Здесь пока пусто...'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => '',
		'menu_name' => 'Ссылки',
	);
 
	$args = array(
		'labels' => $labels,
		'public' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-admin-links',
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 56,
		'supports' => array('title','excerpt','thumbnail'),
		'register_meta_box_cb' => 'xs_add_meta'
	); 
		 
	register_post_type('menu', $args);
}


add_action("admin_init", "xs_add_meta_menu", 0);

function xs_add_meta_menu()
{
	remove_meta_box('postexcerpt', 'menu', '');

	add_meta_box("xs_credits_meta_menu", "Настройки", "xs_credits_meta_menu", "menu", "normal", "low");   
}


function xs_credits_meta_menu( $post ) 
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
	</p><?
}


// Сохраняем параметры

function xs_save_menu($post_id)
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


// создаем новую колонку

add_filter('manage_edit-menu_columns', 'add_views_column_menu', 1);
function add_views_column_menu( $columns )
{
	$num = 1; // после какой по счету колонки вставлять новые

	$new_columns = array(
		'image' => 'Изображение',
	);

	return array_slice($columns, 0, $num) + $new_columns + array_slice($columns, $num);
}

add_action('init', 'xs_menu_register');
add_action('save_post_menu', 'xs_save_menu');

?>