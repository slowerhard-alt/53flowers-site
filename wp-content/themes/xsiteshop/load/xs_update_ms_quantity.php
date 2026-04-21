<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
include $_SERVER['DOCUMENT_ROOT'].'/moysklad/class.php';

if(!is_user_logged_in())
	die();

$is_set = false;
$product_id = (int)$_POST['product_id'];

$store_components = get_store_components_product($product_id);

$ar_compontnt_ids = [];

if(is_array($store_components))
	foreach($store_components as $k => $v)
		$ar_compontnt_ids[] = $k;

if(count($ar_compontnt_ids) == 0)
	die();

// Получаем ассортимент с моего склада

$MoySklad = new MoySklad;
$moysklad_remains = $MoySklad->get_remains();

if(!$moysklad_remains)
{
	echo "error";
	die();
}


// Получаем компоненты с сайта

$ar_components = $wpdb->get_results("SELECT * FROM `xsite_store_components` WHERE `id` IN ('".implode("','", $ar_compontnt_ids)."')");

if(!$ar_components)
	die();

// Перебираем все компоненты с сайта

foreach($ar_components as $component)
{
	$db_ms_components = !empty($component->ms_components)
		? json_decode($component->ms_components, true)
		: [];
	$_db_ms_components = [];
	
	foreach($db_ms_components as $v)
		$_db_ms_components[$v['code']] = $v;
	
	
	$e = explode(',', $component->ms_ids);
	$ar_codes = [];
	$quantity = 0;
	$ar_ms_components = [];
	
	foreach($e as $v)
		if(!empty(trim($v)))
			$ar_codes[] = trim($v);
		
	if(!count($ar_codes))
		continue;
	
	foreach($ar_codes as $code)
	{
		if(isset($moysklad_remains[$code]) || isset($_db_ms_components[$code]))
		{			
			if(!isset($moysklad_remains[$code]))
			{
				$moysklad_remains[$code]['quantity'] = 0;
				$moysklad_remains[$code]['days'] = 0;
			}

			if(isset($_db_ms_components[$code]))
			{
				$moysklad_remains[$code]['code'] = $_db_ms_components[$code]['code'];
				$moysklad_remains[$code]['set_price'] = $_db_ms_components[$code]['set_price'];
				$moysklad_remains[$code]['set_days'] = $_db_ms_components[$code]['set_days'];
			}
			
			$ar_ms_components[] = $moysklad_remains[$code];
		}
	}
		
	if(count($ar_ms_components))
	{
		foreach($ar_ms_components as $v)
			if($v['quantity'] > 0)
				$quantity = $quantity + $v['quantity'];
	}

	if($quantity != $component->ms_quantity)
		$is_set = true;
	
	$wpdb->query("
		UPDATE
			`xsite_store_components`
		SET
			`ms_quantity` = '".$quantity."',
			`ms_components` = '".$wpdb->_real_escape(json_encode($ar_ms_components, JSON_UNESCAPED_UNICODE))."'
		WHERE
			`id` = '".$component->id."'
	");
}

/*
if($is_set)
	update_price_component(0, true);
*/

echo get_structure_for_product($product_id);