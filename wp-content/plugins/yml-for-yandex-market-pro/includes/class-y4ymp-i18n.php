<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin so that it is ready for translation.
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 * @version    6.0.0 (20-03-2025)
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin so that it is ready for translation.
 *
 * @since      0.1.0
 * @package    Y4YMP
 * @subpackage Y4YMP/includes
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
class Y4YMP_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'yml-for-yandex-market-pro',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
