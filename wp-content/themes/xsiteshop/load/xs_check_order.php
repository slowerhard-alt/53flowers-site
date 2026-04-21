<?
global $big_data, $wbdb;

$big_data['not_cache'] = 'y';

include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

$order_search = string_to_int(xs_format($_POST['order']));
	
if(isset($_GET['checkorder']) && !empty($order_search))
{
	$order_search = string_to_int(xs_format($_POST['order']));
	
	if(!$order = $wpdb->get_row("SELECT * FROM `xsite_posts` WHERE `ID` = '".$order_search."' AND `post_type` = 'shop_order'"))
	{
		$db_orders = $wpdb->get_results("
			SELECT 
				p.*,
				pm.`meta_value` AS phone
			FROM 
				`xsite_posts` AS p  
			INNER JOIN `xsite_postmeta` AS pm ON pm.`post_id` = p.`ID` AND pm.`meta_key` = '_billing_phone'
			WHERE
				p.`post_type` = 'shop_order'
		");
		
		if($db_orders)
		{
			foreach($db_orders as $v)
			{
				if(substr($order_search, strlen($order_search)-10) == substr(string_to_int($v->phone), strlen(string_to_int($v->phone))-10))
				{
					$order = $v;
					break;
				}
			}
		}
	}
	
	if($order)
	{
		$statuses = xs_set_order_status('');
		
		?><p>Статус заказ №<?=$order->ID ?>:&nbsp;&nbsp;&nbsp; <strong><?=$statuses[$order->post_status] ?></strong>.</p><?
	}
	else
		echo '<p class="error">Заказ не найден.</p>';
		
	?><p>Так же уточнить детали заказа Вы можете по телефонам:</p><?
	?><div class="phone"><a class="equator__phone-number" href="tel:<?=string_to_int($big_data['phones'][0]) ?>"><?=$big_data['phones'][0] ?></a></div><?
	?><div class="phone"><a class="equator__phone-number" href="tel:<?=string_to_int($big_data['phones'][1]) ?>"><?=$big_data['phones'][1] ?></a></div><?
	
}
else
	echo '<p class="error">Заказ не найден.</p>';