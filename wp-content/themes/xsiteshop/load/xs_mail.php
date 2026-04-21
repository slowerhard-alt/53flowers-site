<?

global $big_data;

$big_data['not_cache'] = 'y';

include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

$headers[] = 'content-type: text/html';

$comment = xs_format($_POST['xs_comment']);
$name = xs_format($_POST['xs_name']);
$phone = xs_format($_POST['xs_phone']);
$send_it = xs_format($_POST['send_it']);
$xs_theme = xs_format($_POST['xs_theme']);
$link = xs_format($_POST['xs_link']);
$email = xs_format($_POST['xs_email']);
$data = $_POST['data'];


if($send_it != 'y')
	die();

if($xs_theme != "Изменение состава товара")
{
	if(empty($phone) && empty($email))
	{
		echo 'error';
		die();
	}

	if(!empty($phone) && !is_phone_valid($phone))
	{
		echo "error-p";
		die();
	}

	if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) === false && !is_phone_valid($email))
	{
		echo "error-e";
		die();
	}
}


$phone = str_replace("+8", "+7", $phone);

$message = "<table border='1px' cellspasing='0' cellpadding='5px'>";

if(!empty($name))
	$message .= "<tr><td>Имя</td><td>".$name."</td></tr>";

if(!empty($phone))
	$message .= "<tr><td>Телефон</td><td>".$phone."</td></tr>";

if(!empty($email))
	$message .= "<tr><td>E-mail</td><td>".$email."</td></tr>";

if(!empty($comment))
	$message .= "<tr><td>Комментарий</td><td>".$comment."</td></tr>";

$message .= "<tr><td>Тема обращения</td><td>".$xs_theme."</td></tr>";
$message .= "<tr><td>Страница, с которой отправлено</td><td><a href='".$link."'>".$link."</a></td></tr>";

if(isset($data) && is_array($data) && count($data) > 0)
{
	foreach($data as $key => $val)
	{
		$message .= "<tr><td>".xs_format($key)."</td><td>".xs_format($val)."</td></tr>";
	}
}

$message .= "</table><br/>
<br/>
----<br/>
Это сообщение отправлено с сайта ".get_option('blogname')." (".get_site_url().")";

if(empty($xs_theme))
	$xs_theme = 'Заявка с сайта';

if(!empty($phone) || !empty($email))
	$email = $big_data['emails'][0];
else
	$email = xs_get_option('xs_store_email');

//$result = wp_mail($email, $xs_theme, $message, $headers); 

$result = true;

if($result) 
{
	if(empty($phone) && !empty($email))
		echo "<p class='good'>Ваша заявка принята, спасибо.</p>";
	else
		echo "<p class='good'>Ваша заявка успешно сформирована. В ближайшее время с Вами свяжется наш менеджер для уточнения деталей. Спасибо за Ваше обращение!<br/><br/></p>";
	
	if(function_exists('add_to_crm') && !empty($phone))
	{
		add_to_crm([
			"SOURCE_ID" => ($xs_theme == "Заказ обратного звонка") ? 26 : 25,
			"TITLE" => $name,
			"NAME" => $name,
			"PHONE" => $phone,
			//"EMAIL" => $email,
			"COMMENTS" => str_replace("\'", '"', $comment),
			"STATUS_ID" => "NEW",
			"UF_CRM_1639660003479" => $link,
			"SOURCE_DESCRIPTION" => $xs_theme,
		]);
	}
	elseif($xs_theme == "Изменение состава товара")
	{
		if(!class_exists('bitrix24'))
			include $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/xsiteshop/include/class/bitrix24.php';

		$b24 = new bitrix24;

		$product_id = (int)$_REQUEST['product_id'];
		
		if($product = get_post($product_id))
		{
			$user_id = 5593;
			
			$b24->add_task([
				'TITLE' => "Изменить состав товара: ".$product->post_title,
				'DESCRIPTION' => "Товар на сайте:\n".get_permalink($product->post_parent > 0 ? $product->post_parent : $product->ID)."\n\nРедактирование товара:\n.".get_bloginfo('url')."/wp-admin/post.php?post=".($product->post_parent > 0 ? $product->post_parent : $product->ID)."&action=edit\n\nКомментарий:\n".$comment,
				'CREATED_BY' => $user_id,
				'FORKED_BY_TEMPLATE_ID' => '',
				'DEADLINE' => date('Y-m-d H:i:s', strtotime('now') + (3600 * 4)),
				'GROUP_ID' => 87,
			], $user_id);
		}
	}
}
else  
	echo 'error';

?>