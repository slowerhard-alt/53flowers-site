<?

// Получаем список опций темы

$big_data['options'] = array();

foreach($options as $v)
{
	if($ar = get_option('xs_options_'.$v['code']))
		$big_data['options'] = array_merge($big_data['options'], $ar);
}

function xs_get_option($option, $default = false, $multiple = false)
{
	global $big_data;
	
	if(isset($big_data['options'][$option]))
	{
		if($multiple)
		{
			$e = explode(';', $big_data['options'][$option]);	
			
			$result = array();
			
			foreach($e as $v)
				$result[] = trim($v);
		}
		else 
			$result = $big_data['options'][$option];
		
		return $result;
	}
	else
		return $default;
}