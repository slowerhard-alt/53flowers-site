<?php

/**
 * Fired during plugin deactivation.
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 * @version    6.0.0 (20-03-2025)
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @package    Y4YMP
 * @subpackage Y4YMP/includes
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
class Y4YMP_Deactivator {

	/**
	 * Triggered when the plugin is deactivated (called once).
	 *
	 * @since    0.1.0
	 * 
	 * @return   void
	 */
	public static function deactivate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

	}

}
