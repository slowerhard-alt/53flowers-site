<?
if(!defined('ABSPATH')) exit;

wc_print_notices();

?><p><? _e( 'There are some issues with the items in your cart (shown above). Please go back to the cart page and resolve these issues before checking out.', 'woocommerce' ) ?></p><?

do_action( 'woocommerce_cart_has_errors' );

?><p><a class="button wc-backward" href="<? echo esc_url( wc_get_page_permalink( 'cart' ) ); ?>"><? _e( 'Return To Cart', 'woocommerce' ) ?></a></p><?
