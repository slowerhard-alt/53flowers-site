<?php

/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link                    https://icopydoc.ru
 * @since                   0.1.0
 * @package                 Y4YMP
 *
 * @wordpress-plugin
 * Plugin Name:             YML for Yandex Market PRO
 * Requires Plugins:        woocommerce, yml-for-yandex-market
 * Plugin URI:              https://icopydoc.ru/category/documentation/yml-for-yandex-market-pro/
 * Description:             Creates a YML-feed to upload to Yandex Market and not only
 * Version:                 6.1.0
 * Requires at least:       5.0
 * Requires PHP:            7.4.0
 * Author:                  Maxim Glazunov
 * Author URI:              https://icopydoc.ru/
 * License:                 GPL-2.0+
 * License URI:             http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:             yml-for-yandex-market-pro
 * Domain Path:             /languages
 * Tags:                    yml, yandex, market, export, woocommerce
 * WC requires at least:    3.0.0
 * WC tested up to:         10.5.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'Y4YMP_MIN_VER', '4.0.0' );

$not_run = false;

// Check php version
if ( version_compare( phpversion(), '7.4.0', '<' ) ) { // не совпали версии
	add_action( 'admin_notices', function () {
		warning_notice( 'notice notice-error',
			sprintf(
				'<strong style="font-weight: 700;">%1$s</strong> %2$s 7.4.0 %3$s %4$s',
				'YML for Yandex Market PRO',
				__( 'plugin requires a php version of at least', 'yml-for-yandex-market-pro' ),
				__( 'You have the version installed', 'yml-for-yandex-market-pro' ),
				phpversion()
			)
		);
	} );
	$not_run = true;
}

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if ( ! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) ) )
	&& ! ( is_multisite()
		&& array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', [] ) ) )
) {
	add_action( 'admin_notices', function () {
		warning_notice(
			'notice notice-error',
			sprintf(
				'<strong style="font-weight: 700;">YML for Yandex Market PRO</strong> %1$s',
				__( 'requires WooCommerce installed and activated', 'yml-for-yandex-market-pro' )
			)
		);
	} );
	$not_run = true;
} else {
	// add support for HPOS
	add_action( 'before_woocommerce_init', function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	} );
}

// Check if YML for Yandex Market is active
$plugin = 'yml-for-yandex-market/yml-for-yandex-market.php';
if ( ! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) ) )
	&& ! ( is_multisite()
		&& array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', [] ) ) )
) {
	add_action( 'admin_notices', function () {
		warning_notice(
			'notice notice-error',
			sprintf(
				'<strong style="font-weight: 700;">YML for Yandex Market PRO</strong> %1$s %2$s %3$s',
				__( 'it is required that', 'yml-for-yandex-market-pro' ),
				'<a href="https://wordpress.org/plugins/yml-for-yandex-market/">YML for Yandex Market</a>',
				__( 'to be installed and activated', 'yml-for-yandex-market-pro' )
			)
		);
	} );
	$not_run = true;
} else { // and checking the minimum version of the basic plugin
	// /home/www/site.ru/wp-content/plugins/yml-for-yandex-market/yml-for-yandex-market.php';
	$basic_plugin_file = plugin_dir_path( __DIR__ ) . $plugin;
	$get_from_headers_arr = [ 
		'ver' => 'Version',
		'name' => 'Plugin Name'
	];
	$basic_plugin_data = get_file_data( $basic_plugin_file, $get_from_headers_arr );
	define( 'Y4YMP_BASIC_PLUGIN_VERSION', $basic_plugin_data['ver'] );
	if ( version_compare( Y4YMP_BASIC_PLUGIN_VERSION, Y4YMP_MIN_VER, '<' ) ) {
		add_action( 'admin_notices', function () {
			warning_notice(
				'notice notice-error',
				sprintf( '<span %1$s>%2$s PRO</span> %3$s <a href="%4$s">%2$s</a> (v.%5$s %6$s). %7$s %2$s',
					'style="font-weight: 700;"',
					'YML for Yandex Market',
					__( 'requires', 'yml-for-yandex-market-pro' ),
					'https://wordpress.org/plugins/yml-for-yandex-market/',
					Y4YMP_MIN_VER,
					__( 'or later', 'yml-for-yandex-market-pro' ),
					__( 'Please update the plugin', 'yml-for-yandex-market-pro' ),
				)
			);
		} );
		$not_run = true;
	} else {
		$not_run = apply_filters( 'y4ymp_f_nrv', $not_run, [ 'basic_plugin_data' => $basic_plugin_data ] );
	}
	unset( $get_from_headers_arr );
	unset( $basic_plugin_data );
}
// end Check if YML for Yandex Market is active

if ( ! function_exists( 'warning_notice' ) ) {
	/**
	 * Display a notice in the admin plugins page. Usually used in a @hook `admin_notices`.
	 * 
	 * @since 0.1.0
	 * 
	 * @param string $class
	 * @param string $message
	 * 
	 * @return void
	 */
	function warning_notice( $class = 'notice', $message = '' ) {
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}

if ( false === $not_run ) {
	unset( $not_run );

	/**
	 * Currently plugin version.
	 * Start at version 0.1.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define( 'Y4YMP_PLUGIN_VERSION', '6.1.0' );

	// /home/p135/www/site.ru/wp-content/plugins/yml-for-yandex-market/
	define( 'Y4YMP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

	// yml-for-yandex-market-pro - псевдоним плагина
	define( 'Y4YMP_PLUGIN_SLUG', wp_basename( dirname( __FILE__ ) ) );

	// yml-for-yandex-market-pro/yml-for-yandex-market-pro.php
	// полный псевдоним плагина (папка плагина + имя главного файла)
	define( 'Y4YMP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

	/**
	 * The plugin autoloader.
	 */
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-y4ymp-autoloader.php';
	new Y4YMP_Autoloader( Y4YMP_PLUGIN_DIR_PATH, 'Y4YMP' );

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-y4ymp-activator.php.
	 * 
	 * @return void
	 */
	function activate_y4ymp() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-y4ymp-activator.php';
		Y4YMP_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-y4ymp-deactivator.php.
	 * 
	 * @return void
	 */
	function deactivate_y4ymp() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-y4ymp-deactivator.php';
		Y4YMP_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_y4ymp' );
	register_deactivation_hook( __FILE__, 'deactivate_y4ymp' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-y4ymp.php';

	/**
	 * The plugin function.
	 */
	require_once plugin_dir_path( __FILE__ ) . 'function.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	function run_y4ymp() {

		$plugin = new Y4YMP();
		$plugin->run();

	}

	run_y4ymp();

}
