<?
if(xs_get_option('xs_news'))
{
	$labels = apply_filters( "post_type_labels_{$post_type}", $labels );

	add_filter('post_type_labels_post', 'rename_posts_labels');

	function rename_posts_labels( $labels )
	{
		$new = array(
			'name'                  => 'Новости компании',
			'singular_name'         => 'Новость',
			'add_new'               => 'Добавить новость',
			'add_new_item'          => 'Добавить новость',
			'edit_item'             => 'Редактировать новость',
			'new_item'              => 'Новая новость',
			'view_item'             => 'Просмотреть новость',
			'search_items'          => 'Поиск новостей',
			'not_found'             => 'Новостей не найдено.',
			'not_found_in_trash'    => 'Новостей в корзине не найдено.',
			'parent_item_colon'     => '',
			'all_items'             => 'Все новости',
			'archives'              => 'Архивы новостей',
			'insert_into_item'      => 'Вставить в новость',
			'uploaded_to_this_item' => 'Загруженные для этой новости',
			'featured_image'        => 'Миниатюра новости',
			'filter_items_list'     => 'Фильтровать список новостей',
			'items_list_navigation' => 'Навигация по списку новостей',
			'items_list'            => 'Список новостей',
			'menu_name'             => 'Новости',
			'name_admin_bar'        => 'Новость',
		);

		return (object) array_merge( (array) $labels, $new );
	}


	function unregister_taxonomy_post_tag()
	{
		register_taxonomy('post_tag', array());
		register_taxonomy('category', array());
	}

	add_action('init', 'unregister_taxonomy_post_tag'); 
}
else
{
	//add_action('init', 'xs_remove_news_post_type');
	
	//function xs_remove_news_post_type()
	//{
	//	register_post_type('post', array());
	//}
}