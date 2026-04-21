<?

// Удаляем лишние поля из формы заказа

add_filter('woocommerce_checkout_fields' , 'custom_override_checkout_fields', 99999);

function custom_override_checkout_fields($fields) 
{
	unset($fields['billing']['billing_country']);
	unset($fields['billing']['billing_city']);
	unset($fields['billing']['billing_state']);
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_last_name']);
	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_email']);
	unset($fields['shipping']['shipping_first_name']);
	unset($fields['shipping']['shipping_last_name']);
	unset($fields['shipping']['shipping_company']);
	unset($fields['shipping']['shipping_address_1']);
	unset($fields['shipping']['shipping_address_2']);
	unset($fields['shipping']['shipping_city']);
	unset($fields['shipping']['shipping_postcode']);
	unset($fields['shipping']['shipping_country']);
	unset($fields['shipping']['shipping_state']);
	return $fields;
}


// Изменяем стандартные поля 

add_filter('woocommerce_billing_fields', 'custom_woocommerce_billing_fields');

function custom_woocommerce_billing_fields($fields) {

	$fields['billing_first_name']['label'] = "Ваше имя";
	$fields['billing_first_name']['class'] = array('form-row');
	$fields['billing_first_name']['placeholder'] = "";

	/*
	$fields['billing_email']['required'] = 1;
	$fields['billing_email']['placeholder'] = "";
	*/
	
	$fields['billing_phone']['label'] = "Ваш номер телефона";
	$fields['billing_phone']['placeholder'] = "+_ (___) ___-__-__";
	$fields['billing_phone']['class'] = array('form-row');
	$fields['billing_phone']['priority'] = 20;

	$fields['billing_address_1']['required'] = 0;
	$fields['billing_address_1']['label'] = "Адрес доставки";
	$fields['billing_address_1']['placeholder'] = "Напишите «Нет» и мы уточним адрес у получателя";

	return $fields;
}


// Создаём новые поля

add_action('woocommerce_after_checkout_billing_form', 'xs_add_fields');
 
function xs_add_fields( $checkout ){
 
    // Описываем поле
	
	/*
    woocommerce_form_field('_postcard', 
		[
			'type'          => 'radio',
			'required'    	=> true,
			'class'         => ['wpbl-field', 'form-row-wide'],
			'label'         => '',
			'label_class'   => 'wpbl-label',
			'options'    	=> [ 
				'y'    => 'Приложить записки',
				'n'    => 'Записка не нужна'
			]
		], 
		(!empty($checkout->get_value('_postcard')) ? $checkout->get_value('_postcard') : 'n')
	);
	
    woocommerce_form_field('_postcard_text', 
		[
			'type'          => 'textarea',
			'required'    	=> false,
			'class'         => ['wpbl-field', 'form-row-wide'],
			'label'         => 'Текст записки',
			'label_class'   => 'wpbl-label',
		], 
		$checkout->get_value('_postcard_text')
	);
	
    woocommerce_form_field('_postcard_text', 
		[
			'type'          => 'textarea',
			'required'    	=> false,
			'class'         => ['wpbl-field', 'form-row-wide'],
			'label'         => 'Дата доставки',
			'label_class'   => 'wpbl-label',
		], 
		$checkout->get_value('_postcard_text')
	);
	*/
}
 
 
// Сохраняем поля

add_action('woocommerce_checkout_update_order_meta', 'xs_save_fields');

function xs_save_fields($order_id)
{		
    if(!empty($_POST['_delivery_date']))
        update_post_meta($order_id, '_delivery_date', sanitize_text_field($_POST['_delivery_date']));    

    if(!empty($_POST['_postcard']))
        update_post_meta($order_id, '_postcard', sanitize_text_field($_POST['_postcard']));   
		
    if($_POST['_postcard'] == 'y' && !empty($_POST['_postcard_text']))
        update_post_meta($order_id, '_postcard_text', sanitize_text_field($_POST['_postcard_text']));    
		
    if(!empty($_POST['_is_recipient']))
        update_post_meta($order_id, '_is_recipient', sanitize_text_field($_POST['_is_recipient']));    
		
    if(/*$_POST['_is_recipient'] == 'y' && */!empty($_POST['_recipient_name']))
        update_post_meta($order_id, '_recipient_name', sanitize_text_field($_POST['_recipient_name']));    
		
    if(/*$_POST['_is_recipient'] == 'y' && */!empty($_POST['_recipient_phone']))
        update_post_meta($order_id, '_recipient_phone', sanitize_text_field(str_replace("_", "", $_POST['_recipient_phone'])));    
		
    if(/*$_POST['_is_recipient'] == 'y' && */!empty($_POST['_recipient_is_call']))
        update_post_meta($order_id, '_recipient_is_call', sanitize_text_field($_POST['_recipient_is_call']));    
		
    if($_POST['_recipient_is_call'] == 'n' && !empty($_POST['_delivery_time']))
        update_post_meta($order_id, '_delivery_time', sanitize_text_field($_POST['_delivery_time']));    
		
    if(!empty($_POST['billing_phone']))
        update_post_meta($order_id, '_billing_phone', sanitize_text_field(str_replace("_", "", $_POST['billing_phone'])));    
		
    if(!empty($_POST['_delivery_time_exact']))
        update_post_meta($order_id, '_delivery_time_exact', sanitize_text_field($_POST['_delivery_time_exact']));    
}


// Выдаём ошибку, если поля не заполнены

add_action('woocommerce_checkout_process', 'xs_save_fields_validation');
 
function xs_save_fields_validation() 
{
    /*
	if(($_POST['shipping_method'][0] == 'flat_rate:5' || $_POST['shipping_method'][0] == 'free_shipping:2') && empty($_POST['billing_address_1']))
        wc_add_notice('<strong>Адрес доставки</strong> является обязательным полем.', 'error');    
	*/
	
    if(empty($_POST['_delivery_date']))
        wc_add_notice('<strong>Дата доставки</strong> является обязательным полем.', 'error');    
	
	/*
	if($_POST['_postcard'] == 'y' && empty($_POST['_postcard_text']))
        wc_add_notice('<strong>Текст записки</strong> является обязательным полем.', 'error');    
	*/
	
	/*
	if($_POST['_is_recipient'] == 'y' && empty($_POST['_recipient_name']))
        wc_add_notice('<strong>Имя получателя</strong> является обязательным полем.', 'error');    
		
	if($_POST['_is_recipient'] == 'y' && empty($_POST['_recipient_phone']))
        wc_add_notice('<strong>Телефон получателя</strong> является обязательным полем.', 'error');    
	*/
	
	/*
	if($_POST['_recipient_is_call'] == 'n' && empty($_POST['_delivery_time']))
        wc_add_notice('<strong>Желаемое время доставки</strong> является обязательным полем.', 'error');    
	*/
	
	/*
	if($_POST['shipping_method'][0] == 'flat_rate:5' && empty($_POST['_delivery_time_exact']))
        wc_add_notice('<strong>Точное время доставки</strong> является обязательным полем.', 'error');    
	*/
}