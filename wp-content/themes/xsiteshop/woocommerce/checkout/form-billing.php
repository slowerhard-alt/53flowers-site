<?

if(!defined('ABSPATH')) exit;

?><div class="woocommerce-billing-fields"><?
	?><h3 class="align-left"><strong><? _e('Укажите свои данные:'); ?></strong></h3><?

	do_action('woocommerce_before_checkout_billing_form', $checkout);

	?><div class="woocommerce-billing-fields__field-wrapper"><?
		
		foreach($checkout->get_checkout_fields('billing') as $key => $field) 
		{
			pre($field);
			woocommerce_form_field( $key, $field, $checkout->get_value($key)); 
		}
		
	?></div><?

	do_action( 'woocommerce_after_checkout_billing_form', $checkout );
	
?></div><?

if(!is_user_logged_in() && $checkout->is_registration_enabled())
{
	?><div class="woocommerce-account-fields"><?
		
		if(!$checkout->is_registration_required())
		{
			?><div class="create-account-link"><?
				?><input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <? checked((true === $checkout->get_value('createaccount') || (true === apply_filters('woocommerce_create_account_default_checked', false))), true) ?> type="checkbox" name="createaccount" value="1" /><?
				?><label for="createaccount" class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox"><? _e('Зарегистрировать вас?'); ?></label><?
			?></div><?

		}

		do_action('woocommerce_before_checkout_registration_form', $checkout);

		if($checkout->get_checkout_fields('account'))
		{
			?><div class="create-account"><?
			
				foreach($checkout->get_checkout_fields('account') as $key => $field)
				{
					woocommerce_form_field($key, $field, $checkout->get_value($key));
				}
				
				?><div class="clear"></div><?
			?></div><?
		}

		do_action('woocommerce_after_checkout_registration_form', $checkout);
		
	?></div><?
}
