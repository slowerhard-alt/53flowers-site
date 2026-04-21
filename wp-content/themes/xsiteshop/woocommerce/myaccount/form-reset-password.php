<?
if(!defined('ABSPATH')) exit;

wc_print_notices(); ?>

<form method="post" class="woocommerce-ResetPassword lost_reset_password">

	<p><? echo apply_filters( 'woocommerce_reset_password_message', __( 'Enter a new password below.', 'woocommerce') ); ?></p>

	<p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-first">
		<label for="password_1"><? _e( 'New password', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_1" id="password_1" />
	</p>
	<p class="woocommerce-FormRow woocommerce-FormRow--last form-row form-row-last">
		<label for="password_2"><? _e( 'Re-enter new password', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_2" id="password_2" />
	</p>

	<input type="hidden" name="reset_key" value="<? echo esc_attr( $args['key'] ); ?>" />
	<input type="hidden" name="reset_login" value="<? echo esc_attr( $args['login'] ); ?>" />

	<div class="clear"></div>

	<? do_action( 'woocommerce_resetpassword_form' ); ?>

	<p class="woocommerce-FormRow form-row">
		<input type="hidden" name="wc_reset_password" value="true" />
		<input type="submit" class="btn woocommerce-Button button" value="<? esc_attr_e( 'Save', 'woocommerce' ); ?>" />
	</p>

	<? wp_nonce_field( 'reset_password' ); ?>

</form>
