<form method="post" class="hide" id="formlogin"><?
	
	?><div class="title">Войти в личный кабинет</div><?
	
	do_action( 'woocommerce_login_form_start' ); 
	
	?><div class="input"><?
		?><label for="xs_username"><? _e( 'Username or email address', 'woocommerce' ); ?><?
		?><input type="text" name="username" id="xs_username" value="<? if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" /><?
	?></div><?
	?><div class="input"><?
		?><label for="xs_password"><? _e( 'Password', 'woocommerce' ); ?></label><?
		?><input type="password" name="password" id="xs_password" /><?
	?></div><?

	do_action( 'woocommerce_login_form' );
	
	?><input name="rememberme" type="checkbox" id="xs_rememberme" value="forever" /><?
	?><label for="xs_rememberme" class="inline"><? _e( 'Remember me', 'woocommerce' ) ?></label><?

	wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' );

	?><div class="xs_flex xs_middle buttons"><?
		?><div class="form-row"><?
			?><input type="submit" class="btn" name="login" value="<? esc_attr_e( 'Войти', 'woocommerce' ); ?>" /><?
		?></div><?
		?><div class="lost_password"><?
			?><a href="<? echo esc_url( wp_lostpassword_url() ); ?>"><? _e( 'Lost your password?', 'woocommerce' ); ?></a><?
		?></div><?
	?></div><?
	
	?><div class="register_line"><?
		?>У вас ещё нет аккаунта? <a href="<?=get_permalink(7) ?>">Зарегистрируйтесь</a><?
	?></div><?
	
	do_action( 'woocommerce_login_form_end' );

?></form><?
