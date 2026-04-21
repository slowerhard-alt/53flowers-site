<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 *
 * @package    Y4YMP
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( is_multisite() ) {
	delete_blog_option( get_current_blog_id(), 'y4ymp_version' );
	delete_blog_option( get_current_blog_id(), 'y4ymp_license_status' );
	delete_blog_option( get_current_blog_id(), 'y4ymp_license_date' );
	delete_blog_option( get_current_blog_id(), 'y4ymp_order_id' );
	delete_blog_option( get_current_blog_id(), 'y4ymp_order_email' );
} else {
	delete_option( 'y4ymp_version' );
	delete_option( 'y4ymp_license_status' );
	delete_option( 'y4ymp_license_date' );
	delete_option( 'y4ymp_order_id' );
	delete_option( 'y4ymp_order_email' );
}
wp_cache_flush();