<?

if(!defined('ABSPATH')) exit;

if(is_user_logged_in() || 'no' === get_option('woocommerce_enable_checkout_login_reminder'))
	return;

$info_message  = apply_filters('woocommerce_checkout_login_message', __('У вас есть учётная запись?'));
$info_message .= ' <a href="'.get_bloginfo('template_directory').'/load/xs_loginform.php?redirect_url='.get_bloginfo('url').$_SERVER['REDIRECT_URL'].'" class="showlogin fancybox" data-type="ajax">'. __('Нажмите для авторизации').'</a>';
wc_print_notice($info_message, 'notice');

