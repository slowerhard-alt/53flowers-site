<?
if(!defined('ABSPATH')) exit;

wc_print_notices(); ?>

<form method="post" class="woocommerce-ResetPassword lost_reset_password">

	<p><? echo apply_filters( 'woocommerce_lost_password_message', __( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?></p>

	<p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-first">
		<label for="user_login"><? _e( 'Username or email', 'woocommerce' ); ?></label>
		<input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" />
	</p>

	<div class="clear"></div>

	<? do_action( 'woocommerce_lostpassword_form' ); ?>

	<p class="woocommerce-FormRow form-row">
		<input type="hidden" name="wc_reset_password" value="true" />
		<input type="submit" class="woocommerce-Button button" value="<? esc_attr_e( 'Reset Password', 'woocommerce' ); ?>" />
	</p>

	<? wp_nonce_field( 'lost_password' ); ?>

</form>
