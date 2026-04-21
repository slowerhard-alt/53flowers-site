<?
require_once("xs_cache_get_path.php");

if(!is_user_logged_in())
{
	foreach($_COOKIE as $k => $v)
		if(mb_substr($k, 0, 20, 'utf-8') == 'wordpress_logged_in_')
			setcookie($k, "", time()-3600, "/");
}


// Добавляем кнопку "Очистить кеш" в панель управления

function wp_admin_bar_new_link_()
{
	global $wp_admin_bar;

	$u = get_bloginfo('url').str_replace(
		["&clear_cache=y", "?clear_cache=y", "?clear_all_cache=y", "&clear_all_cache=y"], 
		[""], 
		$_SERVER['REQUEST_URI']
	);
	
	$wp_admin_bar->add_menu(
		array(
			'id'    => 'wp-admin-bar-clear_cache_page',
			'title' => __('Очистить кеш страницы'),
			'href'  => mb_strpos($u, '?', 0, "utf-8") === false ? $u."?clear_cache=y" : $u."&clear_cache=y",
		)
	);
	$wp_admin_bar->add_menu(
		array(
			'id'    => 'wp-admin-bar-clear_cache_all',
			'parent' => 'wp-admin-bar-clear_cache_page',
			'title' => __('Очистить весь кеш'),
			'href'  => mb_strpos($u, '?', 0, "utf-8") === false ? $u."?clear_all_cache=y" : $u."&clear_all_cache=y",
		)
	);
	$wp_admin_bar->add_menu(
		array(
			'id'    => 'wp-admin-bar-clear_cache_image',
			'parent' => 'wp-admin-bar-clear_cache_page',
			'title' => __('Очистить кеш картинок'),
			'href'  => mb_strpos($u, '?', 0, "utf-8") === false ? $u."?clear_image_cache=y" : $u."&clear_image_cache=y",
		)
	);
}

if(!is_admin())
	add_action('wp_before_admin_bar_render', 'wp_admin_bar_new_link_');


// Очищаем кэш

if($_GET['clear_cache'] == 'y')
{
	$cache_data = get_cache_path();
	@unlink($cache_data['cache_path']);
}

if(isset($_GET['clear_all_cache']) && $_GET['clear_all_cache'] == 'y')
	xs_clear_cache();

if(isset($_GET['clear_image_cache']) && $_GET['clear_image_cache'] == 'y')
	xs_clear_cache(true);


// Читим кеш при публикации записей

add_action('save_post_product', 'xs_clear_cache', 1, 0);

