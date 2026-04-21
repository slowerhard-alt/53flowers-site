<?
if(!defined('ABSPATH')) exit;

?>
<p><?
	printf(
		__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce' ),
		'<mark class="order-number">' . $order->get_order_number() . '</mark>',
		'<mark class="order-date">' . date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ) . '</mark>',
		'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>'
	);
?></p>

<? if ( $notes = $order->get_customer_order_notes() ) : ?>
	<h2><? _e( 'Order Updates', 'woocommerce' ); ?></h2>
	<ol class="woocommerce-OrderUpdates commentlist notes">
		<? foreach ( $notes as $note ) : ?>
		<li class="woocommerce-OrderUpdate comment note">
			<div class="woocommerce-OrderUpdate-inner comment_container">
				<div class="woocommerce-OrderUpdate-text comment-text">
					<p class="woocommerce-OrderUpdate-meta meta"><? echo date_i18n( __( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); ?></p>
					<div class="woocommerce-OrderUpdate-description description">
						<? echo wpautop( wptexturize( $note->comment_content ) ); ?>
					</div>
	  				<div class="clear"></div>
	  			</div>
				<div class="clear"></div>
			</div>
		</li>
		<? endforeach; ?>
	</ol>
<? endif; ?>

<? do_action( 'woocommerce_view_order', $order_id ); ?>
