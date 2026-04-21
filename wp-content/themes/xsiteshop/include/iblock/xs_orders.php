<?
add_action("admin_init", "xs_add_meta_order_store", 0);

function xs_add_meta_order_store()
{
	add_meta_box('xs_order_meta', 'Параметры заказа', 'xs_order_meta', 'shop_order', 'side', 'low');
	//add_meta_box('xs_store_order', 'Состав товаров', 'xs_store_order', 'shop_order', 'normal', 'low');
}


function xs_order_meta($post) 
{
	wp_nonce_field( 'xs_meta_box_nonce', 'meta_box_nonce' ); 
	global $wpdb, $big_data;
	
	?><div class="xs_field">
		<div class="xs_label">Дата доставки:</div>
		<input type="text" class="xs_input xs_date" name="_delivery_date" value="<?=get_post_meta($post->ID, '_delivery_date', true) ?>" />
	</div><?
	
	?><div class="xs_field xs_field_postcard">
		<div class="xs_label">Текст записки:</div>
		<textarea class="xs_input" name="_postcard_text"><?=get_post_meta($post->ID, '_postcard_text', true) ?></textarea>
	</div><?
	
	/*
	?><div class="xs_field">
		<div class="xs_label">Получатель другой?</div>
		<label>
			<input type="radio" name="_is_recipient" value="y"<?=get_post_meta($post->ID, '_is_recipient', true) == 'y' ? ' checked' : '' ?> onchange="if(!jQuery(this).is(':checked')) jQuery('.xs_field_recipient').addClass('xs_hide'); else jQuery('.xs_field_recipient').removeClass('xs_hide');" />
			Да
		</label>
		&nbsp;&nbsp;&nbsp;
		<label>
			<input type="radio" name="_is_recipient" value="n"<?=get_post_meta($post->ID, '_is_recipient', true) != 'y' ? ' checked' : '' ?> onchange="if(jQuery(this).is(':checked')) jQuery('.xs_field_recipient').addClass('xs_hide'); else jQuery('.xs_field_recipient').removeClass('xs_hide');" />
			Нет
		</label>
	</div><?
	*/
	
	?><div class="xs_field xs_field_recipient<?//=get_post_meta($post->ID, '_is_recipient', true) == 'y' ? '' : ' xs_hide' ?>">
		<div class="xs_label">Имя получателя</div>
		<input type="text" class="xs_input" name="_recipient_name" value="<?=get_post_meta($post->ID, '_recipient_name', true) ?>" />
	</div><?
	
	?><div class="xs_field xs_field_recipient<?//=get_post_meta($post->ID, '_is_recipient', true) == 'y' ? '' : ' xs_hide' ?>">
		<div class="xs_label">Телефон получателя</div>
		<input type="text" class="xs_input xs_phone" name="_recipient_phone" value="<?=get_post_meta($post->ID, '_recipient_phone', true) ?>" />
	</div><?
	 
	?><div class="xs_field xs_field_recipient_is_call">
		<div class="xs_label">Время доставки</div>
		<input type="text" class="xs_input" name="_delivery_time" value="<?=get_post_meta($post->ID, '_delivery_time', true) ?>" />
	</div><?
	
	/*
	?><div class="xs_field">
		<div class="xs_label">Точное время доставки</div>
		<input type="text" class="xs_input xs_time" name="_delivery_time_exact" value="<?=get_post_meta($post->ID, '_delivery_time_exact', true) ?>" />
	</div><?
	*/
}


// Действия при смене статусов заказа

add_action('woocommerce_order_status_pending', 'xs_set_order_status_pending');

function xs_set_order_status_pending($order_id) 
{
    //sms()
}


// Действия при сохранении заказа

add_action('save_post_shop_order', 'xs_save_shop_order');

function xs_save_shop_order($post_id)
{
	global $wpdb, $big_data;
	
	// Сохраняем дополнительные поля
	
    if(isset($_POST['_delivery_date']))
        update_post_meta($post_id, '_delivery_date', sanitize_text_field($_POST['_delivery_date']));    

    if(isset($_POST['_postcard']))
        update_post_meta($post_id, '_postcard', sanitize_text_field($_POST['_postcard']));   
		
    if(isset($_POST['_postcard_text']))
        update_post_meta($post_id, '_postcard_text', sanitize_text_field($_POST['_postcard_text']));    
		
    if(isset($_POST['_is_recipient']))
        update_post_meta($post_id, '_is_recipient', sanitize_text_field($_POST['_is_recipient']));    
		
    if(isset($_POST['_recipient_name']))
        update_post_meta($post_id, '_recipient_name', sanitize_text_field($_POST['_recipient_name']));    
		
    if(isset($_POST['_recipient_phone']))
        update_post_meta($post_id, '_recipient_phone', sanitize_text_field(str_replace("_", "", $_POST['_recipient_phone'])));    
		
    if(isset($_POST['billing_phone']))
        update_post_meta($post_id, '_billing_phone', sanitize_text_field(str_replace("_", "", $_POST['billing_phone'])));    
		
    if(isset($_POST['_recipient_is_call']))
        update_post_meta($post_id, '_recipient_is_call', sanitize_text_field($_POST['_recipient_is_call']));    
		
    if(isset($_POST['_delivery_time']))
        update_post_meta($post_id, '_delivery_time', sanitize_text_field($_POST['_delivery_time']));    
		
    if(isset($_POST['_delivery_time_exact']))
        update_post_meta($post_id, '_delivery_time_exact', sanitize_text_field($_POST['_delivery_time_exact']));    
		
    if(isset($_POST['_delivery_expenses']))
        update_post_meta($post_id, '_delivery_expenses', sanitize_text_field($_POST['_delivery_expenses'])); 
}
