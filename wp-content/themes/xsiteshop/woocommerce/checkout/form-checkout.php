<?
if(!defined( 'ABSPATH')) exit;

if(xs_get_option('xs_deactive_checkout'))
{
	echo xs_get_option('xs_deactive_checkout_text');
	
	return;
}

?><div class="xs_cart_container"><?

	do_action('woocommerce_before_cart'); 
	do_action('woocommerce_before_checkout_form', $checkout);

	?><form action="<?=esc_url(WC()->cart->get_cart_url()) ?>" method="post" class="cart_form"><?

		do_action( 'woocommerce_before_cart_table' );

		?><div class="xs_overflow"><?
			?><table class="shop_table cart"><?
				?><thead><?
					?><tr><?
						?><th class="product-remove"></th><?
						?><th class="product-thumbnail"></th><?
						?><th class="product-name"><? _e('Товар') ?></th><?
						?><th class="product-price"><? _e('Цена') ?></th><?
						?><th class="product-quantity"><? _e('Кол-во') ?></th><?
						?><th class="product-subtotal"><? _e('Итого') ?></th><?
					?></tr><?
				?></thead><?
				?><tbody><? 
				
					do_action('woocommerce_before_cart_contents');

					foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item)
					{
						$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
						$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

						if($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) 
						{
							?><tr class="<?=esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>"><?

								?><td class="product-remove"><?
									?><div class="remove_container"><?
										?><a href="<?=esc_url(WC()->cart->get_remove_url($cart_item_key)) ?>" rel="nofollow" class="xs_middle xs_flex active remove"><span class="close"></span><span class="text">Удалить из корзины</span></a><? 
									?></div><?						
								?></td><?

								?><td class="product-thumbnail"><?
									
									$src = wp_get_attachment_image_src($_product->image_id, 'shop_catalog');
									$src = xs_img_resize($src[0], 48, 48);
									
									?><div class="image"><?
										?><a target="_blank" href="<?=$_product->get_permalink($cart_item) ?>"><?
											?><img src="<? echo $src  ?>" alt="" /><?
										?></a><?
									?></div><?
								
								?></td><?

								?><td class="product-name"><?
									
									if ( ! $product_permalink ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
									} else {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
									}

									do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

									// Meta data.
									echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

									// Backorder notification.
									if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
									}

									?><span class="product-name_price"><?
										echo $_product->get_price_html();
									?></span><?
						
								?></td><?

								?><td class="product-price"><?
									?><div class="xs_prices"><? 
									
										echo $_product->get_price_html();
										
									?></div><?
								?></td><?

								?><td class="product-quantity"><?
								
									if($_product->is_sold_individually())
										$product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
									else
									{
										?><div class="xs_flex xs_count_container"><?
											?><div class="count"><?
											
												?><input type="text" data-max="<?=$_product->backorders_allowed() ? '' : $_product->get_stock_quantity() ?>" name="cart[<?=$cart_item_key ?>][qty]" class="input-text qty text" value="<?=$cart_item['quantity'] ?>" /><?
												
											?></div><?
											?><div class="buttons"><?
												?><span class="plus"></span><?
												?><span class="minus"></span><?
											?></div><?
										?></div><?
									}
									
									echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key);
								
								?></td><?

								?><td class="product-subtotal"><?
									
									echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);

								?></td><?
							?></tr><?
						}
					}
					
					if(WC()->cart->coupons_enabled()) 
					{ 
						?><tr><?
							?><td colspan="6" class="actions"><?

								?><div class="coupon xs_flex xs_start"><?

									?><div class="coupon_code"><?
										?><label for="coupon_code"><? _e('Купон на скидку', 'woocommerce') ?>:</label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" placeholder="<? _e('Введите купон'); ?>" value="" /><?
									?></div><?
									?><div class="buttons"><?
										?><label>&nbsp;</label><?
										?><input type="submit" class="button btn" name="apply_coupon" value="<? _e('Применить') ?>" /><?
									?></div><?
									
									do_action('woocommerce_cart_coupon');

								?></div><?

								?><input type="hidden" name="update_cart" value="y" /><?
								?><input type="hidden" name="apply_coupon" value="y" /><?
								
								do_action('woocommerce_cart_actions');
								wp_nonce_field('woocommerce-cart');
								
							?></td><?
						?></tr><?
					}
					
				?></tbody><?
			?></table><?
		?></div><?

		if(!WC()->cart->coupons_enabled()) 
		{
			?><input type="hidden" name="update_cart" value="y" /><?
			do_action('woocommerce_cart_actions');
			wp_nonce_field('woocommerce-cart');
		}

	?></form><?


	if(!$checkout->enable_signup && !$checkout->enable_guest_checkout && !is_user_logged_in()) 
	{
		echo apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce'));
		return;
	}

	$get_checkout_url = apply_filters('woocommerce_get_checkout_url', WC()->cart->get_checkout_url()); 

	?><form name="checkout" method="post" class="checkout woocommerce-checkout" action="<? echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data"><?

		do_action('woocommerce_checkout_before_customer_details');
		
		?><div class="col2-set xs_flex xs_wrap" id="customer_details"><?
			?><div class="col-1"><?
			
				?><div class="woocommerce-billing-fields"><?
					?><h3 class="align-left"><strong><? _e('Данные для доставки:'); ?></strong></h3><?
				
					do_action('woocommerce_before_checkout_billing_form', $checkout);

					?><div class="woocommerce-billing-fields__field-wrapper"><?
						
						foreach($checkout->get_checkout_fields('billing') as $key => $field) 
						{
							if($key == 'billing_address_1')
								$field['autocomplete'] = "off";
							
							woocommerce_form_field($key, $field, $checkout->get_value($key)); 
							
							if($key == 'billing_phone')
							{
								?><p class="form-row woocommerce-validated" id="_is_recipient_field" data-priority="">
									<span class="woocommerce-input-wrapper">
										<input type="checkbox" name="_is_recipient" id="_is_recipient" value="1" class="input-checkbox"<?=$checkout->get_value("_is_recipient") ? " checked" : "" ?> onchange="
										
											if(jQuery(this).is(':checked'))
											{
												jQuery('.block_is_recipient').removeClass('hide')
												jQuery('.block_is_recipient input[type=text]').val('')
											}
											else
											{
												jQuery('.block_is_recipient').addClass('hide')
												jQuery('.block_is_recipient input[type=text]').val('Нет')
											}
											
										">
										<label class="checkbox" for="_is_recipient">Заказ получает другой человек&nbsp;<span class="optional">(необязательно)</span></label>
									</span>
								</p>
								
								<div class="block_is_recipient<?=$checkout->get_value("_is_recipient") ? '' : ' hide' ?> form-row xs_flex xs_wrap" data-show="y"><?
								
									woocommerce_form_field("_recipient_name", [
										'required'    	=> false,
										'label'         => 'Имя получателя',
										'class'			=> ['validate-phone'],
										'placeholder'	=> 'Напишите «Нет», если получаете Вы',
									], "Нет" /* $checkout->get_value("_recipient_name") */ );
									
									woocommerce_form_field("_recipient_phone", [
										'type' 			=> "tel",
										'placeholder'	=> 'Напишите «Нет», если получаете Вы',
										'class'			=> ['validate-phone'],
										'required'    	=> false,
										'label'         => 'Телефон получателя',
									], "Нет" /* $checkout->get_value("_recipient_phone") */ );
									
								?></div><?
						
								woocommerce_form_field("_delivery_date", [
									'type'          => 'text',
									'required'    	=> true,
									'label'         => 'Дата доставки',
									'class'			=> ['xs_date', 'validate-phone'],
									'placeholder'	=> '__.__.____',
								], (!empty($checkout->get_value("_delivery_date")) ? $checkout->get_value("_delivery_date") : date('d.m.Y')));
								
								woocommerce_form_field("_delivery_time", [
									'type'			=> 'text',
									'required'    	=> false,
									'class'			=> ['xs_margin'], 
									'label'         => 'Время доставки',
									'placeholder'	=> 'Напишите «Нет» и мы уточним время у получателя',
								], $checkout->get_value("_delivery_time"));
							}
						}
						
						woocommerce_form_field("_postcard_text", [
							'type'          => 'textarea',
							'required'    	=> false,
							'label'         => 'Текст записки',
							'placeholder'	=> 'Не используйте смайлы',
						], $checkout->get_value("_postcard_text"));
						
						/*
						woocommerce_form_field("_recipient_is_call", [
							'type'          => 'radio',
							'required'    	=> true,
							'label'         => '',
							'class'			=> ['xs_radio', 'xs_nolabel'], 
							'options'    	=> [ 
								'y'    => 'Позвонить получателю для уточнения времени и&nbsp;адреса',
								'n'    => 'Везти без звонка в&nbsp;указанный промежуток времени',
							]
						], (!empty($checkout->get_value("_recipient_is_call")) ? $checkout->get_value("_recipient_is_call") : 'y'));
						
						?><div class="block_recipient_is_call<?=$checkout->get_value("_recipient_is_call") == 'n' ? '' : ' hide' ?> form-row xs_flex xs_wrap" data-show="n"><?
						
							woocommerce_form_field("_delivery_time", [
								'type'			=> 'text',
								'required'    	=> true,
								'class'			=> ['xs_margin'], 
								'label'         => 'Желаемое время доставки',
								'placeholder'	=> 'например, с 14 до 15',
							], $checkout->get_value("_delivery_time"));
					
						?></div><?
						*/
						
					?></div><?

					do_action( 'woocommerce_after_checkout_billing_form', $checkout);
					
					foreach ($checkout->checkout_fields['order'] as $key => $field)
						woocommerce_form_field($key, $field, $checkout->get_value($key));
					
				?></div><?
				
				?><input type="hidden" name="billing_country" value="RU" /><? 
				?><input type="hidden" name="shipping_country" value="RU" /><? 
				
			?></div><?
			?><div class="col-2"><?
				
				do_action('woocommerce_checkout_before_order_review');

				?><div id="order_review" class="woocommerce-checkout-review-order"><?
					
					do_action('woocommerce_checkout_order_review');
					
				?></div><?

				do_action('woocommerce_checkout_after_order_review');
				
			?></div><?
		?></div><?
		
		do_action('woocommerce_checkout_after_customer_details');

	?></form><?

	do_action('woocommerce_after_checkout_form', $checkout);
?></div>