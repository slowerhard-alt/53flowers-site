<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

//echo get_bloginfo('url')."/wp-admin/post.php";
//include $_SERVER['DOCUMENT_ROOT'].'/moysklad/class.php';

//$MoySklad = new MoySklad;
//$moysklad_remains = $MoySklad->get_remains();

//pre($moysklad_remains);

/*
if(!class_exists('bitrix24'))
	include $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/xsiteshop/include/class/bitrix24.php';

$b24 = new bitrix24;

pre($b24->add_task([
	'TITLE' => "Тест задача",
	'DESCRIPTION' => "Описание",
	'CHECKLIST' => [
		["TITLE" => 'Поменять цены на ФВ'],
		["TITLE" => 'Поменять цены на озон'],
		["TITLE" => 'Сгенерировать фид для вк и загрузить его в вк'],
		["TITLE" => 'Сгенерировать фид для ЯМ и обновить его в ЯМ'],
		["TITLE" => 'Сгенерировать фид для авито'],
		["TITLE" => 'Сгенерировать фид для Купера'],
	]
]));
*/

// message_to_telegram("Тест");

/*
$sql = explode('),(', $sql);

foreach($sql as $v)
{
	$e = explode(",'", $v);
	
	$id = str_replace("(", "", $e[0]);
	$ms_ids = str_replace("'", "", $e[13]);
	
	$wpdb->query("UPDATE `xsite_store_components` SET `ms_ids` = '".$ms_ids."' WHERE `id` = '".$id."'");
}
*/

/*
update_price_component(0, true);

include "sql.php";

$result = [];
$ar_result = [];

$e = explode(");", $sql);

foreach($e as $v)
{
	$_e = explode("),(", $v);
	
	foreach($_e as $_v)
	{
		if(mb_strpos($_v, ",'product',", 0, 'utf-8') !== false)
		{
			$__e = explode("','", $_v);
			
			$description = $__e[2];

			$___e = explode(",", $__e[0]);
			$id = preg_replace('/[^0-9]/', '', $___e[0]);
			
			$ar_result[$id] = $description;
		}
	}
}

foreach($ar_result as $k => $v)
{
	$wpdb->query("UPDATE `xsite_posts` SET `post_content` = '".$wpdb->_real_escape($v)."' WHERE `ID` = '".$k."'");
}

*/

//actual_product_content(20552);

/*
$components = $wpdb->get_results("SELECT * FROM `xsite_store_components`");

foreach($components as $v)
{
	$sale_rules = $v->sale_percent > 0
		? json_encode(['type' => '', 'percent' => $v->sale_percent], JSON_UNESCAPED_UNICODE)
		: "{}";
	
	$wpdb->get_results("
		UPDATE 
			`xsite_store_components`
		SET
			`sale_rules` = '".$sale_rules."'
		WHERE
			`id` = '".$v->id."'
	");
}
*/

/*
include $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/xsiteshop/include/class/bitrix24.php';

$b24 = new bitrix24;

$arg = [
	"SOURCE_ID" => 26, // 26 - обратный звонок, 25 - заказ в 1 клик
	"TITLE" => "Тест",
	"NAME" => "Тест",
	"PHONE" => "89563265458",
	"EMAIL" => "test@test.ru",
	"COMMENTS" => "комментарии",
	"STATUS_ID" => "NEW",
	"UF_CRM_1639660003479" => "https://53flowers.ru",
	"SOURCE_DESCRIPTION" => "Оформление заказа тест",
];

pre($b24->add_lid($arg));
*/

/*
if(is_user_logged_in())
{
	$count = 0;
	
	$ar_products = $wpdb->get_results("SELECT * FROM `xsite_posts` WHERE `post_type` IN ('product', 'product_variation')");
	
	foreach($ar_products as $v)
	{
		set_product_price($v->ID);
		
		if($v->post_type == 'product')
			$count++;
	}
	
	?><p>Цены обновлены, кэш очищен. Обработано товаров - <?=$count ?></p><?
}
*/