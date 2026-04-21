<?
if(!defined('ABSPATH')) exit;
?>
<tr class="shipping">
	<th><? echo wp_kses_post( $package_name ); ?></th>
	<td data-title="<? echo esc_attr( $package_name ); ?>">
		<? if ( 1 < count( $available_methods ) ) : ?>
			<ul id="shipping_method">
				<? foreach ( $available_methods as $method ) : ?>
					<li>
						<?
							printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />
								<label for="shipping_method_%1$d_%2$s">%5$s</label>',
								$index, sanitize_title( $method->id ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ), wc_cart_totals_shipping_method_label( $method ) );
							do_action( 'woocommerce_after_shipping_rate', $method, $index );
						?>
					</li>
				<? endforeach; ?>
			</ul>
		<? elseif ( 1 === count( $available_methods ) ) :  ?>
			<?
				$method = current( $available_methods );
				printf( '%3$s <input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d" value="%2$s" class="shipping_method" />', $index, esc_attr( $method->id ), wc_cart_totals_shipping_method_label( $method ) );
				do_action( 'woocommerce_after_shipping_rate', $method, $index );
			?>
		<? elseif ( ! WC()->customer->has_calculated_shipping() ) : ?>
			<? echo wpautop( __( 'Shipping costs will be calculated once you have provided your address.', 'woocommerce' ) ); ?>
		<? else : ?>
			<? echo apply_filters( is_cart() ? 'woocommerce_cart_no_shipping_available_html' : 'woocommerce_no_shipping_available_html', wpautop( __( 'There are no shipping methods available. Please double check your address, or contact us if you need any help.', 'woocommerce' ) ) ); ?>
		<? endif; ?>

		<? if ( $show_package_details ) : ?>
			<? echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; ?>
		<? endif; ?>

		<? if ( is_cart() && ! $index ) : ?>
			<? woocommerce_shipping_calculator(); ?>
		<? endif; ?>
	</td>
</tr>
