<?
global $wpdb;

// Регистрируем тип записей "Слайдер"

function xs_banner_register() {
 
	$labels = array(
		'name' => __('Все баннеры'),
		'singular_name' => __('Баннер'),
		'add_new' => __('Добавить баннер'),
		'add_new_item' => __('Добавить новый баннер'),
		'edit_item' => __('Изменить баннер'),
		'new_item' => __('Новый баннер'),
		'view_item' => __('Посмотреть баннер'),
		'search_items' => __('Искать баннер'),
		'not_found' =>  __('Здесь пока пусто...'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => '',
		'menu_name' => 'Баннеры',
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-format-image',
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 57,
		'supports' => array('title','excerpt','thumbnail'),
		'register_meta_box_cb' => 'xs_add_meta'
	); 
		 
	register_post_type( 'banner' , $args );
}


add_action("admin_init", "xs_add_meta_banner", 0);

function xs_add_meta_banner()
{
	remove_meta_box( 'postimagediv', 'banner', '' );
	remove_meta_box( 'postexcerpt', 'banner', '');

	add_meta_box('postimagediv', 'Изображение баннера', 'post_thumbnail_meta_box', 'banner', 'normal', 'high');
	add_meta_box("xs_credits_meta_banner", "Настройки", "xs_credits_meta_banner", "banner", "normal", "low");   
	add_meta_box("xs_banner_text", "Содержимое баннера", "xs_banner_text", "banner", "normal", "high");
}


function xs_banner_text( $post ) 
{
	wp_nonce_field( 'xs_meta_box_nonce', 'meta_box_nonce' ); 
	
	?><p>Заголовок баннера (можно использовать HTML код):<br/>
		<input type="text" style="width: 100%; margin-top:12px;" name="excerpt" value="<?=$post->post_excerpt ?>" /><?
	?></p><?
	
	?><p>Краткое описание баннера (можно использовать HTML код):<br/><?
		?><textarea name="content" style="height:150px;" id="excerpt"><?=$post->post_content ?></textarea><? 
	?></p><?
}


function xs_credits_meta_banner( $post ) 
{	
	wp_nonce_field( 'xs_meta_box_nonce', 'meta_box_nonce' ); 
	$xs_options = get_post_meta($post->ID, 'xs_options', true);
	
	?><p><label>
		Ссылка:<br/>
		<input type="text" style="width: 100%;" name="xs_url" value="<?=$xs_options['url'] ?>" />
	</label></p>
	<p><label>
		<input type="checkbox" name="xs_target" value="y" <? if ($xs_options['target'] == 'y') echo "checked" ?> />
		Открывать в новой вкладке?
	</label>
	</p>
	<p><label>
		<input type="checkbox" name="xs_arrow" value="y" <? if ($xs_options['arrow'] == 'y') echo "checked" ?> />
		Отображать стрелку?
	</label>
	</p>
	<p>
		Цвет текста:
		<input type="text" name="xs_color" class="color" value="<?=$xs_options['color'] ?>">
	</p>
	<p>
		Фильтр изображения:
		<input type="text" name="xs_color_filter" class="color" value="<?=$xs_options['color_filter'] ?>">
	</p>
	<p>
		Цвет фона 1:
		<input type="text" name="xs_color_bg" class="color" value="<?=$xs_options['color_bg'] ?>">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		Цвет фона 2:
		<input type="text" name="xs_color_bg2" class="color" value="<?=$xs_options['color_bg2'] ?>">
	</p><br/>
	
	<a href="/wp-admin/admin.php?page=xs_setting&tab=banners" target="_blank">Общие настройки баннеров</a><? 
}


// Сохраняем параметры

function xs_save_details_banner($post_id)
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

add_filter('manage_edit-banner_columns', 'add_views_column_banner', 1);
function add_views_column_banner( $columns ){
	$columns['image'] = 'Изображение';
	return $columns;
}


add_action('init', 'xs_banner_register');
add_action('save_post', 'xs_save_details_banner');

?>