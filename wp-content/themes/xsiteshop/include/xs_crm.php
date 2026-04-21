<?

// Передача лидов в CRM

function add_to_crm($arg)
{	
	if(!class_exists('bitrix24'))
		include $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/xsiteshop/include/class/bitrix24.php';

	$b24 = new bitrix24;
	
	return $b24->add_lid($arg);
}


// Отправляем лид в CRM при оформлении заказа

//add_action('woocommerce_new_order', 'add_to_crm_in_wc'); 

function add_to_crm_in_wc($order_id)
{
	$order = wc_get_order($order_id);

	$comment[] = "Заказ №".$order_id;
	
	/*
	$comment[] = "Сумма заказа: ".$order->data['total'];
	$comment[] = "Адрес: ".$order->data['billing']['address_1'];
	$comment[] = "Способ оплаты: ".$order->data['payment_method_title'];
	*/
	
	if(!empty($order->data['customer_note']))
		$comment[] = "Комментарий: ".$order->data['customer_note'];
	
	add_to_crm(array(
		'TITLE' => $order->data['billing']['first_name'],
		'NAME' => $order->data['billing']['first_name'],
		'PHONE_WORK' => $order->data['billing']['phone'],
		'EMAIL_WORK' => $order->data['billing']['email'],
		'COMMENTS' => implode("; ", $comment),
		'STATUS_ID' => 'NEW',
		'SOURCE_DESCRIPTION' => "Оформление заказа",
	));
}

