<?
if(!defined('ABSPATH')) exit;

if(is_user_logged_in()) return;

?><form method="post" class="login" <? if ( $hidden ) echo 'style="display:none;"'; ?>>

	<? do_action( 'woocommerce_login_form_start' ); ?>

	<? if ( $message ) echo wpautop( wptexturize( $message ) ); ?>

	<p class="form-row form-row-first">
		<label for="username"><? _e( 'Username or email', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="username" id="username" />
	</p>
	<p class="form-row form-row-last">
		<label for="password"><? _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input class="input-text" type="password" name="password" id="password" />
	</p>
	<div class="clear"></div>

	<? do_action( 'woocommerce_login_form' ); ?>

	<p class="form-row">
		<? wp_nonce_field( 'woocommerce-login' ); ?>
		<input type="submit" class="button" name="login" value="<? _e( 'Login', 'woocommerce' ); ?>" />
		<input type="hidden" name="redirect" value="<? echo esc_url( $redirect ) ?>" />
		<label for="rememberme" class="inline">
			<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <? _e( 'Remember me', 'woocommerce' ); ?>
		</label>
	</p>
	<p class="lost_password">
		<a href="<? echo esc_url( wc_lostpassword_url() ); ?>"><? _e( 'Lost your password?', 'woocommerce' ); ?></a>
	</p>

	<div class="clear"></div>

	<? do_action( 'woocommerce_login_form_end' ); ?>

</form>
