<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<p>
	<?php
		echo sprintf( esc_attr__( 'Здравствуйте, %s%s%s (не %2$s? %sВыйти%s)', 'woocommerce' ), '<strong>', esc_html( $current_user->display_name ), '</strong>', '<a href="' . esc_url( wc_get_endpoint_url( 'customer-logout', '', wc_get_page_permalink( 'myaccount' ) ) ) . '">', '</a>' );
	?>
</p>
<p>
	<?php
		echo sprintf( esc_attr__( 'В личном кабинете Вы можете просматривать %1$sпоследние заказы%2$s, управлять %3$sадресами доставки%2$s, а также %4$sизменять пароль и данные учетной записи%2$s.', 'woocommerce' ), '<a href="' . esc_url( wc_get_endpoint_url( 'orders' ) ) . '">', '</a>', '<a href="' . esc_url( wc_get_endpoint_url( 'edit-address' ) ) . '">', '<a href="' . esc_url( wc_get_endpoint_url( 'edit-account' ) ) . '">' );
	?>
</p>
<?php
	do_action( 'woocommerce_account_dashboard' );
	do_action( 'woocommerce_before_my_account' );
	do_action( 'woocommerce_after_my_account' );
?>
