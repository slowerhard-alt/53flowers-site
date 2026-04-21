<?
global $wpdb;

// Регистрируем тип записей "Отзывы"

function xs_review_register() 
{ 
	$labels = array(
		'name' => _x('Отзывы о магазине', 'post type general name'),
		'singular_name' => _x('Отзыв', 'post type singular name'),
		'add_new' => _x('Добавить отзыв', 'review item'),
		'add_new_item' => __('Добавить новый отзыв', 'Triton'),
		'edit_item' => __('Изменить отзыв', 'Triton'),
		'new_item' => __('Новый отзыв', 'Triton'),
		'view_item' => __('Посмотреть отзыв', 'Triton'),
		'search_items' => __('Искать отзыв', 'Triton'),
		'not_found' =>  __('Здесь пока пусто...', 'Triton'),
		'not_found_in_trash' => __('В корзине ничего не найдено', 'Triton'),
		'parent_item_colon' => '',
		'menu_name' => 'Отзывы',
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-format-chat',
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 54,
		'supports' => array('title','editor','thumbnail'),
		'register_meta_box_cb' => 'xs_add_meta_review'
	); 
		 
	register_post_type( 'review' , $args );
}

function xs_add_meta_review()
{
	remove_meta_box('wpseo_meta', 'review', 'normal');

	add_meta_box("xs_credits_meta_answer", "Ответ администратора", "xs_credits_meta_answer", "review", "normal", "low");   
}


function xs_credits_meta_answer($post) 
{
	wp_nonce_field('xs_meta_box_nonce', 'meta_box_nonce');
	
	$id = 'excerpt';
	$excerpt = htmlspecialchars_decode($post->post_excerpt);
	wp_editor($excerpt, $id, array(
		'media_buttons' => 0,
		'textarea_rows' => 250,
	));
}

add_action('init', 'xs_review_register');
