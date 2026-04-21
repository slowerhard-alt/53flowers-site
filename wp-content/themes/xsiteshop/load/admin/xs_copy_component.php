<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

global $wpdb, $big_data;

if(!current_user_can('administrator'))
{
	header("HTTP/1.1 403 Forbidden");
	die();
}

if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'xs_admin_action'))
{
	header("HTTP/1.1 403 Forbidden");
	die("Invalid nonce");
}

$variation_id = (int)xs_format($_POST['variation_id']);

if($variation_id == 0)
	die();

if(!$product = get_post($variation_id))
	die();

$products = get_posts([
	'post_parent' => $product->post_parent,
	'post_type' => $product->post_type,
	'exclude' => [$variation_id],
	'posts_per_page' => -1
]);

if($products)
{
	$ar_components = get_store_components_product($variation_id, true);

	$wpdb->query('START TRANSACTION');

	$success = true;

	foreach($products as $v)
	{
		if($wpdb->query($wpdb->prepare("DELETE FROM `xsite_store_products` WHERE `product_id` = %d AND `site` = '53flowers'", $v->ID)) === false)
		{
			$success = false;
			break;
		}

		if($ar_components)
		{
			foreach($ar_components as $_v)
			{
				$quantity = round((float)$_v->quantity, 1);
				$sale = (int)$_v->sale_in_product;
				$is_markup = ($_v->is_markup == 'y') ? 'y' : '';
				$is_set_stock_ms = ($_v->is_set_stock_ms == 'y') ? 'y' : '';

				if($quantity <= 0)
					continue;

				$existing = $wpdb->get_row($wpdb->prepare(
					"SELECT * FROM `xsite_store_products` WHERE `product_id` = %d AND `component_id` = %d AND `site` = '53flowers'",
					$v->ID, $_v->component_id
				));

				if($existing)
				{
					$res = $wpdb->query($wpdb->prepare(
						"UPDATE `xsite_store_products` SET `quantity` = %f, `name_in_product` = %s, `sale_in_product` = %d, `is_markup` = %s, `is_set_stock_ms` = %s WHERE `product_id` = %d AND `component_id` = %d AND `site` = '53flowers'",
						$quantity, $_v->name_in_product, $sale, $is_markup, $is_set_stock_ms, $v->ID, $_v->component_id
					));
				}
				else
				{
					$res = $wpdb->query($wpdb->prepare(
						"INSERT INTO `xsite_store_products` SET `product_id` = %d, `component_id` = %d, `quantity` = %f, `name_in_product` = %s, `sale_in_product` = %d, `is_markup` = %s, `is_set_stock_ms` = %s, `site` = '53flowers'",
						$v->ID, $_v->component_id, $quantity, $_v->name_in_product, $sale, $is_markup, $is_set_stock_ms
					));
				}

				if($res === false)
				{
					$success = false;
					break 2;
				}
			}
		}
	}

	if($success)
	{
		$wpdb->query('COMMIT');

		// Пересчитываем цены после успешного копирования
		foreach($products as $v)
			set_product_price($v->ID);
	}
	else
	{
		$wpdb->query('ROLLBACK');
		header("HTTP/1.1 500 Internal Server Error");
		die("Copy failed, changes rolled back");
	}
}
