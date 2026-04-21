<?php
/**
 * WooCommerce - Bitrix24 CRM - Integration.
 *
 * @author itgalaxycompany
 *
 * @wordpress-plugin
 * Plugin Name: WooCommerce - Bitrix24 CRM - Integration
 * Requires Plugins: woocommerce
 * Plugin URI: https://wordpress-plugins.ru/product/woocommerce-bitrix24-crm-integration-woocommerce-bitriks24-crm-integracziya/
 * Description: Allows you to integrate your WooCommerce and Bitrix24 CRM
 * Version: 1.69.0
 * Author: wordpress-plugins.ru
 * Author URI: https://wordpress-plugins.ru
 * Text Domain: wc-bitrix24-integration
 * Domain Path: /languages/
 */

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registration and load of translations.
 *
 * @see https://developer.wordpress.org/reference/functions/load_plugin_textdomain/
 */
\add_action('init', function () {
    \load_plugin_textdomain('wc-bitrix24-integration', false, dirname(\plugin_basename(__FILE__)) . '/languages');
});

/**
 * Use composer autoloader.
 */
require plugin_dir_path(__FILE__) . 'vendor/autoload.php';

/**
 * Register plugin uninstall hook.
 *
 * @see https://developer.wordpress.org/reference/functions/register_uninstall_hook/
 */
\register_uninstall_hook(__FILE__, [Bootstrap::class, 'pluginUninstall']);

/**
 * Load plugin.
 */
Bootstrap::getInstance(__FILE__);
