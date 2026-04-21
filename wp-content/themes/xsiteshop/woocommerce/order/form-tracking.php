<?
if(!defined('ABSPATH')) exit;

global $post;

?>

<form action="<? echo esc_url( get_permalink( $post->ID ) ); ?>" method="post" class="track_order">

	<p><? _e( 'To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'woocommerce' ); ?></p>

	<p class="form-row form-row-first"><label for="orderid"><? _e( 'Order ID', 'woocommerce' ); ?></label> <input class="input-text" type="text" name="orderid" id="orderid" placeholder="<? _e( 'Found in your order confirmation email.', 'woocommerce' ); ?>" /></p>
	<p class="form-row form-row-last"><label for="order_email"><? _e( 'Billing Email', 'woocommerce' ); ?></label> <input class="input-text" type="text" name="order_email" id="order_email" placeholder="<? _e( 'Email you used during checkout.', 'woocommerce' ); ?>" /></p>
	<div class="clear"></div>

	<p class="form-row"><input type="submit" class="button" name="track" value="<? _e( 'Track', 'woocommerce' ); ?>" /></p>
	<? wp_nonce_field( 'woocommerce-order_tracking' ); ?>

</form>
