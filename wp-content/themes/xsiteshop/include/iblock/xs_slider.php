<?
global $wpdb;

// Регистрируем тип записей "Слайдер"

function xs_slider_register() {
 
	$labels = array(
		'name' => __('Все слайды'),
		'singular_name' => __('Слайд'),
		'add_new' => __('Добавить слайд'),
		'add_new_item' => __('Добавить новый слайд'),
		'edit_item' => __('Изменить слайд'),
		'new_item' => __('Новый слайд'),
		'view_item' => __('Посмотреть слайд'),
		'search_items' => __('Искать слайд'),
		'not_found' =>  __('Здесь пока пусто...'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => '',
		'menu_name' => 'Слайды',
	);
 
	$args = array(
		'labels' => $labels,
		'public' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-format-gallery',
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 57,
		'supports' => array('title','excerpt','thumbnail'),
		'register_meta_box_cb' => 'xs_add_meta'
	); 
		 
	register_post_type( 'slider' , $args );
}


add_action("admin_init", "xs_add_meta_slider", 0);

function xs_add_meta_slider()
{
	remove_meta_box( 'postimagediv', 'slider', '' );
	remove_meta_box( 'postexcerpt', 'slider', '');

	add_meta_box('postimagediv', 'Изображение слайда', 'post_thumbnail_meta_box', 'slider', 'normal', 'high');
	add_meta_box("xs_credits_meta", "Настройки", "xs_credits_meta", "slider", "normal", "low");   
	add_meta_box("xs_slide_text", "Содержимое слайда", "xs_slide_text", "slider", "normal", "low");
}


function xs_slide_text( $post ) 
{
	wp_nonce_field( 'xs_meta_box_nonce', 'meta_box_nonce' ); 
	
	?><p>Заголовок слайда (можно использовать HTML код):<br/>
		<input type="text" style="width: 100%; margin-top:12px;" name="excerpt" value="<?=$post->post_excerpt ?>" /><?
	?></p><?
	
	?><p>Краткое описание слайда (можно использовать HTML код):<br/><?
		?><textarea name="content" style="height:150px;" id="excerpt"><?=$post->post_content ?></textarea><? 
	?></p><?
}


function xs_credits_meta( $post ) 
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
	</label></p>
	<p><label>
		Расположение<br /> "left, center, right":<br/>
		<input type="text" style="width: 100%;" name="xs_position" value="<?=$xs_options['position'] ?>" />
	</label></p>
	<br/><br/>
	
	<a href="/wp-admin/admin.php?page=xs_setting&tab=xs_slider" target="_blank">Общие настройки слайдера</a><? 
}


// Сохраняем параметры

function xs_save_details($post_id)
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


function xs_unescape( $str )
{
	return str_replace(
		array ( '&lt;', '&gt;', '&quot;', '&amp;', '&nbsp;', '&amp;nbsp;' )
	,   array ( '<',    '>',    '"',      '&',     ' ', ' ' )
	,   $str
	);
}


// создаем новую колонку

add_filter('manage_edit-slider_columns', 'add_views_column_slider', 1);
function add_views_column_slider( $columns )
{
	$num = 1; // после какой по счету колонки вставлять новые

	$new_columns = array(
		'image' => 'Изображение',
	);

	return array_slice($columns, 0, $num) + $new_columns + array_slice($columns, $num);
}


// заполняем колонку данными

add_filter('manage_posts_custom_column', 'fill_views_column_slider', 10, 3);
function fill_views_column_slider($column_name, $id) 
{
	if( $column_name == 'image' ){
		$url = wp_get_attachment_url( get_post_thumbnail_id($id) );
		echo "<img src='".$url."' style='max-width:150px; max-height:80px;' />";
	} else
		return;
}

add_action('init', 'xs_slider_register');
add_action('save_post', 'xs_save_details');

?>