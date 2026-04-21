<?

add_filter('pre_site_transient_update_core',create_function('$a', "return null;"));
wp_clear_scheduled_hook('wp_version_check');

remove_action( 'load-update-core.php', 'wp_update_plugins' );
add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
wp_clear_scheduled_hook( 'wp_update_plugins' );

remove_action('load-update-core.php','wp_update_themes');
add_filter('pre_site_transient_update_themes',create_function('$a', "return null;"));
wp_clear_scheduled_hook('wp_update_themes');

function xs_clear_admin()
{
	remove_menu_page('plugins.php');
	remove_menu_page('tools.php');
	
}

add_action('admin_menu', 'xs_clear_admin');


// Отключаем стандартные виджеты WP

function true_remove_default_widget() 
{
	unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Recent_Comments');
	unregister_widget('WP_Widget_Search');
	unregister_widget('WP_Widget_Tag_Cloud');
	unregister_widget('WC_Widget_Layered_Nav_Filters');
}
 
add_action( 'widgets_init', 'true_remove_default_widget', 20 );
