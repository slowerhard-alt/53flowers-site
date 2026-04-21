<?php

/**
 * Fired during plugin activation.
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 * @version    6.0.0 (20-03-2025)
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Y4YMP
 * @subpackage Y4YMP/includes
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
class Y4YMP_Activator {

	/**
	 * Triggered when the plugin is activated (called once).
	 *
	 * @since    0.1.0
	 * 
	 * @return   void
	 */
	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( is_multisite() ) {
			add_blog_option( get_current_blog_id(), 'y4ymp_version', Y4YMP_PLUGIN_VERSION );
			add_blog_option( get_current_blog_id(), 'y4ymp_license_status', '' );
			add_blog_option( get_current_blog_id(), 'y4ymp_license_date', '' );
			add_blog_option( get_current_blog_id(), 'y4ymp_order_id', '' ); 
			add_blog_option( get_current_blog_id(), '4yms_order_email', '' );
		} else {
			add_option( 'y4ymp_version', Y4YMP_PLUGIN_VERSION, '', true ); // без автозагрузки
			add_option( 'y4ymp_license_status', '' );
			add_option( 'y4ymp_license_date', '' );
			add_option( 'y4ymp_order_id', '' ); 
			add_option( '4yms_order_email', '' );
		}
	}

}
