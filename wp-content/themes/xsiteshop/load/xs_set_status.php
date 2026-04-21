<?
global $big_data;

$big_data['not_cache'] = 'y';

include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

$product_id = (int)xs_format($_POST['product_id']);

if(!is_user_logged_in() || !$product_id)
	die();

$product = wc_get_product($product_id);

$new_status = $product->get_status() == 'publish'
	? 'draft'
	: 'publish';

$product->set_status($new_status);
$product->save();

$result = (object)[
	'post_status' => $new_status,
	'ID' => $product_id,
];

echo get_set_status_html($result);