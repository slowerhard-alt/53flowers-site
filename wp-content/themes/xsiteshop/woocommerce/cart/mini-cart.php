<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<a href="<?php echo WC()->cart->get_checkout_url(); ?>">
	<span class="cart">Корзина -</span>
	<?php echo WC()->cart->get_cart_subtotal(); ?> 
	&nbsp;
	<span class="count">
		<? echo sizeof( WC()->cart->get_cart() );?>
	</span>
</a> 

<?php do_action( 'woocommerce_before_mini_cart' ); ?>
<div class="cart_product_list">
	<span class="angle"><span></span></span>
	
	<div class="cart_product_list_cont">
		<table class="cart_list product_list_widget <?php echo $args['list_class']; ?>">

			<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

				<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

							$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
							$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
							$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
							?>
							<tr>
								<td class="image">
									<?php if ( ! $_product->is_visible() ) : ?>
										<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
									<?php else : ?>
										<a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>">
											<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
										</a>
									<?php endif; ?>
								</td>
								<td>
									<?php if ( ! $_product->is_visible() ) : ?>
										<?php $product_name; ?>
									<?php else : ?>
										<a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>">
											<?php echo $product_name; ?>
										</a>
									<?php endif; ?>
									<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
								</td>
								<td class="remove">
									<?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" title="%s"><i class="fa fa-times-circle"></i></a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key ); ?>
								</td>
							</tr>
							<?php
						}
					}
				?>

			<?php else : ?>

				<p class="empty center"><?php _e( 'Корзина пуста...' ); ?></p>

			<?php endif; ?>

		</table><!-- end product list -->
	</div>
	<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

		<p class="total"><?php _e( 'Итого' ); ?>: <?php echo WC()->cart->get_cart_subtotal(); ?></p>

		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

		<p class="buttons">
			<a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="button checkout wc-forward"><?php _e( 'Оформить заказ' ); ?></a>
		</p>

	<?php endif; ?>
</div>
<?php do_action( 'woocommerce_after_mini_cart' ); ?>
