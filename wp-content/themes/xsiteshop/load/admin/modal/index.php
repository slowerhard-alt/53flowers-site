<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

global $big_data, $wpdb;

if(
	(!current_user_can('administrator')) || 
	!isset($_REQUEST['modal']) || 
	empty($_REQUEST['modal'])
)
{
	global $post, $page, $wp_query;
	$wp_query->set_404();
	header("HTTP/1.1 404 Not Found");
	get_template_part('404');
	die();
}

$post_data = xs_format($_POST);
$get_data = xs_format($_GET);
$modal = xs_format($_REQUEST['modal']);

$allowed_modals = ['group-add', 'group-components', 'group-detail', 'coefficient-group-add', 'coefficient-group-components', 'coefficient-group-detail'];

if(!in_array($modal, $allowed_modals, true))
{
	header("HTTP/1.1 404 Not Found");
	die("Not found");
}

$modal_path = $modal.".php";

if(file_exists($modal_path))
{
	?><div class="admin_modal admin_modal--<?=esc_attr($modal) ?>"><?

		include $modal_path;
		
	?></div><?
}
else
{
	?><div class="admin_modal"><?

		echo "Файл не найден";
		
	?></div><?
}
