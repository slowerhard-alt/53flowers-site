<?
if(!defined('ABSPATH')) exit;

do_action( 'woocommerce_before_customer_login_form' );

if(get_option('woocommerce_enable_myaccount_registration') === 'yes')
{
	?><div class="xs_register_form"><?
		?><h2><? _e('Регистрация') ?></h2><?

		?><form method="post" class="register"><?

			do_action('woocommerce_register_form_start');

			?><p><?
				?><label for="reg_email"><? _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label><?
				?><input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" value="<? if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" /><?
			?></p><?

			?><p><?
				?><label for="reg_password"><? _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label><?
				?><input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" /><?
			?></p><?

			?><div style="<? echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><? _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div><?

			//do_action( 'woocommerce_register_form' );
			do_action( 'register_form' );
			
			?><div class="privacy"><?
				?><input required id="privacy_register" type="checkbox" checked name="privacy" /><?
				?><label for="privacy_register"><?
					?>Нажимая на кнопку "<? esc_attr_e( 'Register', 'woocommerce' ); ?>" я даю <a href="<?=get_permalink(3444) ?>" target="_blank">согласие на обработку персональных данных</a><?
				?></label><?
			?></div><?

			?><p><?
				wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' );
				?><input type="submit" class="btn" name="register" value="<? _e('Зарегистрироваться') ?>" /><?
			?></p><?

			do_action('woocommerce_register_form_end');
			
			?><p><? _e('У вас уже есть аккаунт?') ?> <a href="<?=get_bloginfo('template_directory') ?>/load/xs_loginform.php?redirect_url=<?=get_bloginfo('url').$_SERVER['REDIRECT_URL'] ?>" data-type="ajax" rel="nofollow" class="log-in fancybox"><? _e('Авторизуйтесь') ?></a></p><?

		?></form><?
	?></div><?
}

do_action( 'woocommerce_after_customer_login_form' ); ?>
