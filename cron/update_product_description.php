<?
$_SERVER['DOCUMENT_ROOT'] = "/home/c/cvetyams/53flowers.com/public_html";

include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

if($ar_posts = $wpdb->get_results("SELECT * FROM `xsite_posts` WHERE `post_type` = 'product'"))
{
	foreach($ar_posts as $post)
		actual_product_content($post);
}