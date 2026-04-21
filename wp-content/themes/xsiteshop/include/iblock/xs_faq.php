<?
global $wpdb;

// Регистрируем тип записей "Популярные вопросы"

function xs_faq_register() {
 
	$labels = array(
		'name' => __('Популярные вопросы'),
		'singular_name' => __('Вопрос'),
		'add_new' => __('Добавить вопрос'),
		'add_new_item' => __('Добавить новый вопрос'),
		'edit_item' => __('Изменить вопрос'),
		'new_item' => __('Новый вопрос'),
		'view_item' => __('Посмотреть вопрос'),
		'search_items' => __('Искать вопрос'),
		'not_found' =>  __('Здесь пока пусто...'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => '',
		'menu_name' => 'FAQ',
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-clipboard',
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 58,
		'supports' => array('title','editor'),
	); 
		 
	register_post_type( 'faq' , $args );
}

add_action('init', 'xs_faq_register');