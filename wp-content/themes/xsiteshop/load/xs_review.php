<?
global $big_data;

$big_data['not_cache'] = 'y';

include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

$post_data = xs_format($_POST['post_data']);

if($post_data['send'] == 'y')
{
	$props = array(
		'post_title'    => wp_strip_all_tags($post_data['name']),
		'post_content'  => $post_data['review'],
		'post_status'   => 'draft',
		'post_author'   => 1,
		'post_type' 	=> 'review'
	);

	$post_id = wp_insert_post($props);
	
	if($post_id)
	{
		if(isset($_FILES['xs_photo']['name']) && !empty($_FILES['xs_photo']['name']))
		{
			require_once(ABSPATH.'wp-admin/includes/image.php');
			require_once(ABSPATH.'wp-admin/includes/file.php');
			require_once(ABSPATH.'wp-admin/includes/media.php');
			
			$attachment_id = media_handle_upload('xs_photo', $post_id);
			
			if(is_wp_error($attachment_id))
				echo "<p class='error'>Ошибка загрузки медиафайла!</p>";
			else
				set_post_thumbnail($post_id, $attachment_id);
		}
		
		
		$message = "<p>На сайте опубликован новый отзыв.</p>
		<p>Он не отобразится на сайте, пока вы его не опубликуете.</p>
		<p><a href='".get_bloginfo('url')."/wp-admin/post.php?post=".$post_id."&action=edit' target='_blank'>Перейти к администрированию отзыва</a></p>
		<br/>
		----<br/>
		Это сообщение отправлено с сайта ".get_option('blogname')." (".get_site_url().")";
		
		wp_mail($big_data['emails'][0], 'Новый отзыв на сайте', $message, 'content-type: text/html'); 
		
		echo "<p class='good'>Спасибо, нам очень важно ваше мнение!<br/>Ваш отзыв будет опубликован после проверки администратором сайта.</p>";
	}
	else
		echo "<p class='error'>Ошибка при добавлении отзыва. Попробуйте пожалуйста ещё позже.</p>";
}