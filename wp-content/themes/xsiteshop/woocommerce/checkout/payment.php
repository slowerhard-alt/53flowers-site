<?
if(!defined('ABSPATH')) exit;

if(!is_ajax())
	do_action('woocommerce_review_order_before_payment');

?><div id="payment" class="woocommerce-checkout-payment"><?

	if(is_super_admin())
	{
		?><a target="_blank" href="/wp-admin/admin.php?page=wc-settings&tab=checkout" class="xs_link_edit"></a><?
	}

	if(WC()->cart->needs_payment())
	{
		?><ul class="wc_payment_methods payment_methods methods"><?
			
			if(!empty($available_gateways))
			{
				foreach($available_gateways as $gateway)
					wc_get_template('checkout/payment-method.php', array('gateway' => $gateway));
			} 
			else 
			{
				echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? __( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce') : __( 'Please fill in your details above to see available payment methods.', 'woocommerce')).'</li>';
			}
			
		?></ul><?
	}
	
	?><div class="form-row place-order"><?
		?><noscript><?
			
			_e('Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce');
			
			?><br/><input type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<? esc_attr_e('Update totals', 'woocommerce'); ?>" /><?
			
		?></noscript><?

		//wc_get_template('checkout/terms.php');
		
		$checkout_text = xs_get_option("xs_checkout_text");
		
		if($checkout_text && !empty($checkout_text)) // Если пользователь авторизован 
		{
			?><div class="privacy"><?
				echo $checkout_text;
			?></div><?
		}

		do_action('woocommerce_review_order_before_submit');

		echo apply_filters('woocommerce_order_button_html', '<input type="submit" class="btn button alt" name="woocommerce_checkout_place_order" id="place_order" value="'.esc_attr($order_button_text).'" data-value="'.esc_attr($order_button_text).'" />');

		?><div class="privacy">
			Ваши персональные данные будут использоваться для обработки вашего заказа, Вашего удобства на этом веб-сайте и для других целей, описанных в <a target="_blank" href="<?=get_permalink(3444) ?>">политика конфиденциальности</a>
		</div><?

		?><div class="privacy">
			<input type="checkbox" name="xs_policy" id="xs_policy_checkout" required checked />
			<label for="xs_policy_checkout">
				Я прочитал(а) и принимаю <a target="_blank" href="<?=get_permalink(3446) ?>">правила и условия сайта</a>
			</label>
		</div><?
		
		do_action('woocommerce_review_order_after_submit');
		wp_nonce_field('woocommerce-process_checkout'); 
		
	?></div><?
?></div><?

if (!is_ajax())
	do_action('woocommerce_review_order_after_payment');
