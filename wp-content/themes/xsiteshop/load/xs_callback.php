<?
global $big_data;

$big_data['not_cache'] = 'y';

header('Content-Type: text/html; charset=utf-8');
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

$phone = xs_format($_POST['phone']);

$message = '';
if (!empty($phone))
{
	$subject = "Заявка на обратный звонок";
	$message .= 'Номер телефона: '.$phone.'<br/>
	<br/>
	--<br/>
	Это сообщение отправлено с сайта '.get_option('blogname').' ('.get_site_url().')';
	
	$headers[] = 'content-type: text/html';
	wp_mail($big_data['emails'][0], $subject, $message, $headers);
}