<?

// Регистрируем таксономии

add_action('init', 'create_taxonomy_producer');

function create_taxonomy_producer()
{
	register_taxonomy('producer', array('product'), array(
		'labels'                => array(
			'name'              => 'Производители',
			'singular_name'     => 'Производитель',
			'search_items'      => 'Поиск производителя',
			'all_items'         => 'Производители',
			'view_item '        => 'Посмотреть производителя',
			'parent_item'       => 'Родительский производитель',
			'parent_item_colon' => 'Родительский производитель',
			'edit_item'         => 'Изменить производителя',
			'update_item'       => 'Обновить производителя',
			'add_new_item'      => 'Добавить нового производителя',
			'new_item_name'     => 'Название нового производителя',
			'menu_name'         => 'Производители',
		),
		'description'           => '',
		'public'                => true,
		'show_in_nav_menus'		=> false,
		'show_in_rest'          => null,
		'rest_base'             => null,
		'hierarchical'          => false,
		'rewrite'               => true,
		'capabilities'          => array(),
		'meta_box_cb'           => 'post_categories_meta_box', 
		'show_admin_column'     => false,
		'_builtin'              => false,
		'show_in_quick_edit'    => null,
	) );
}