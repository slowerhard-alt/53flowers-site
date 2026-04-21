<?
if(substr($_SERVER['SERVER_NAME'], 0, 3) == 'www')
{
	header('Location: '.$_SERVER['HTTP_X_FORWARDED_PROTO'].'://'.str_replace("www.", "", $_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI'], true, 301);
	die();
}

global $big_data;

// Проверка типа устройства

require_once($_SERVER['DOCUMENT_ROOT']."/Mobile_Detect.php");

$detect = new Mobile_Detect();
$big_data['device']['is_mobile'] = $detect->isMobile(); 
$big_data['device']['is_tablet'] = $detect->isTablet(); 
$big_data['device']['is_touch'] = ($detect->isTablet() || $detect->isMobile()) ? true : false;
$big_data['device']['is_pc'] = (!$detect->isTablet() && !$detect->isMobile()) ? true : false;

include $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/xsiteshop/include/xs_cache_get_path.php';

$cache_data = get_cache_path();


// Выводим закешированную страницу

if($cache_data['cache_exists'] && $cache_data['is_cache'])
{
	$LastModified_unix = filemtime($cache_data['cache_path']);
	$LastModified = gmdate("D, d M Y H:i:s \G\M\T", $LastModified_unix);
	$IfModifiedSince = false;
	
	if(isset($_ENV['HTTP_IF_MODIFIED_SINCE']))
		$IfModifiedSince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5)); 
	if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
		$IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
	if($IfModifiedSince && $IfModifiedSince >= $LastModified_unix) 
	{
		header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
		die(); 
	}
	
	header('Last-Modified: '.$LastModified);
	
	include $cache_data['cache_path'];
	die(); 
}


// Выводим незакешированную страницу и добавляем в кеш

ob_start();

include $_SERVER['DOCUMENT_ROOT'].'/index.php';

$content = ob_get_contents();

// Записываем страницу в кэш

if($cache_data['is_cache'] && !is_404())
{
	$file = fopen($cache_data['cache_path'], "w");
		
	fwrite($file, str_replace(
		'<body class="',
		'<body class="cache_page ', 
		$content
	)); 
	
	fclose($file); 
}

ob_end_clean();

echo $content;
