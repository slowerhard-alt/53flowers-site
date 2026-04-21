<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

$post_data = xs_format($_POST);
$get_data = xs_format($_GET);

if(isset($post_data['url']) && !empty($post_data['url']))
{
	if($cache_data = get_cache_path($post_data['url']))
	{
		if(
			!$cache_data['cache_exists'] &&
			mb_strpos($post_data['url'], 'add-to-cart=', 0, 'utf-8') === false
		)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, trim($post_data['url']));
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_REFERER, 'http://yandex.ru');
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla Firefox 3 (compatible; MSIE 6.0; LAS Linux)");
			$code = curl_exec($ch);	
			curl_close($ch); 
				
			if($code && !empty($code))
			{
				$file = fopen($cache_data['cache_path'], "w");
				
				fwrite($file, str_replace(
					array(
						'<body class="',
						"</body>",
					),
					array(
						'<body class="cache_page ',
						"</body>", 
					), $code
				)); 
				
				fclose($file); 
			}
		}
	}
}