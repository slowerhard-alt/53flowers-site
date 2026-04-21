<?
if(!defined('ABSPATH')) exit;

if ( $available_gateways = WC()->payment_gateways->get_available_payment_gateways() ) : ?>
	<form id="add_payment_method" method="post">
		<div id="payment" class="woocommerce-Payment">
			<ul class="woocommerce-PaymentMethods payment_methods methods">
				<?
					// Chosen Method.
					if ( count( $available_gateways ) ) {
						current( $available_gateways )->set_current();
					}

					foreach ( $available_gateways as $gateway ) {
						?>
						<li class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<? echo $gateway->id; ?> payment_method_<? echo $gateway->id; ?>">
							<input id="payment_method_<? echo $gateway->id; ?>" type="radio" class="input-radio" name="payment_method" value="<? echo esc_attr( $gateway->id ); ?>" <? checked( $gateway->chosen, true ); ?> />
							<label for="payment_method_<? echo $gateway->id; ?>"><? echo $gateway->get_title(); ?> <? echo $gateway->get_icon(); ?></label>
							<?
								if ( $gateway->has_fields() || $gateway->get_description() ) {
									echo '<div class="woocommerce-PaymentBox woocommerce-PaymentBox--' . $gateway->id . ' payment_box payment_method_' . $gateway->id . '" style="display: none;">';
									$gateway->payment_fields();
									echo '</div>';
								}
							?>
						</li>
						<?
					}
				?>
			</ul>

			<div class="form-row">
				<? wp_nonce_field( 'woocommerce-add-payment-method' ); ?>
				<input type="submit" class="woocommerce-Button woocommerce-Button--alt button alt" id="place_order" value="<? esc_attr_e( 'Add Payment Method', 'woocommerce' ); ?>" />
				<input type="hidden" name="woocommerce_add_payment_method" id="woocommerce_add_payment_method" value="1" />
			</div>
		</div>
	</form>
<? else : ?>
	<p><? esc_html_e( 'Sorry, it seems that there are no payment methods which support adding a new payment method. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ); ?></p>
<? endif; ?>
