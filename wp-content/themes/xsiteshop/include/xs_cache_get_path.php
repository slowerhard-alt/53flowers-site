<?
if(!function_exists('get_cache_path'))
{
	function get_cache_path($url = '')
	{
		global $big_data;

		if($url == '')
			$url = $_SERVER['REQUEST_URI'];
		
		$r['is_cache'] = true;
		
		
		// Отключаем кэширование для отдельных страниц
		
		$is_auth = false;
		
		foreach($_COOKIE as $k => $v)
		{
			if(mb_substr($k, 0, 20, 'utf-8') == 'wordpress_logged_in_')
			{
				$is_auth = true;
				break;
			}
		}
		
		if(
			$is_auth ||
			(isset($_POST) && count($_POST) > 0) ||
			(isset($_GET['clear_cache']) && $_GET['clear_cache'] == 'y') ||
			(isset($_GET['clear_all_cache']) && $_GET['clear_all_cache'] == 'y') ||
			(isset($_GET['clear_image_cache']) && $_GET['clear_image_cache'] == 'y') ||
			isset($_GET['add-to-cart']) ||
			isset($_GET['logout']) ||
			isset($_REQUEST['unapproved']) ||
			(isset($big_data['not_cache']) && $big_data['not_cache'] == 'y') ||
			mb_strpos($url, '/cart/', 0, 'utf-8') !== false ||
			mb_strpos($url, '/checkout/', 0, 'utf-8') !== false ||
			mb_strpos($url, '/compare/', 0, 'utf-8') !== false ||
			mb_strpos($url, '/account/', 0, 'utf-8') !== false
		)
			$r['is_cache'] = false;
		
		
		// Формируем имя файла кэша изходя из URL
		
		$ar_url = parse_url($url);
		$ar_file_name = [];
		
		$ar_file_name = explode('/', $ar_url['path']);
		
		if(!empty($ar_url['query']))
		{
			parse_str($ar_url['query'], $ar_get);
			
			foreach($ar_get as $k => $v)
			{
				$k = mb_strtolower($k, 'utf-8');
				$v = mb_strtolower($v, 'utf-8');
				
				if(
					substr($k, 0, 4) == 'utm_' ||
					$k == 'cm_id' ||
					$k == 'yclid' ||
					$k == 'ysclid' ||
					$k == 'clear_all_cache' ||
					$k == 'clear_cache' ||
					$k == 'clear_image_cache' ||
					$k == '_' ||
					$k == 'logout'
				)
					continue;
					
				$ar_file_name[] = $k.(!empty($v) ? '-'.$v : '');
			}
		}
		
		foreach($ar_file_name as $k => $v)
			if(empty($v))
				unset($ar_file_name[$k]);
		
		$r['cache_file_name'] = trim(
			str_replace(
				['/', '?', '&amp;', '&', '=', '--', ',', '.'], 
				'-', 
				implode('-', $ar_file_name)
			),  
			'-'
		);
		
		if(empty($r['cache_file_name']))
			$r['cache_file_name'] = 'home';
		
		if($big_data['device']['is_mobile'])
			$r['cache_file_name'] .= "-m";
		
		$r['cache_path'] = $_SERVER['DOCUMENT_ROOT'].'/wp-content/uploads/xs_cache/pages/'.$r['cache_file_name'];
		$r['cache_exists'] = file_exists($r['cache_path']) ? true : false;
		
		return $r;
	}
}