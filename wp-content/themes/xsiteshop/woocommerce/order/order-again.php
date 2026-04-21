<?
if(!defined('ABSPATH')) exit;

?><p class="order-again"><?
	?><a href="<? echo esc_url( wp_nonce_url( add_query_arg( 'order_again', $order->id ) , 'woocommerce-order_again' ) ); ?>" class="button"><? _e( 'Order Again', 'woocommerce' ); ?></a><?
?></p><?
