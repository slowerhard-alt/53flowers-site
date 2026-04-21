<?
global $wpdb;

// Регистрируем тип записей "Табы"

function xs_tabs_register() {
 
	$labels = array(
		'name' => __('Табы'),
		'singular_name' => __('Таб'),
		'add_new' => __('Добавить таб'),
		'add_new_item' => __('Добавить новый таб'),
		'edit_item' => __('Изменить таб'),
		'new_item' => __('Новый таб'),
		'view_item' => __('Посмотреть таб'),
		'search_items' => __('Искать таб'),
		'not_found' =>  __('Здесь пока пусто...'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => '',
		'menu_name' => 'Табы',
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-excerpt-view',
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 56,
		'supports' => array('title','editor'),
	); 
		 
	register_post_type( 'tabs' , $args );
}

add_action('init', 'xs_tabs_register');