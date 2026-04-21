<?

global $big_data;

if(isset($_COOKIE["xs_compare"]) && !empty($_COOKIE["xs_compare"]))
{
	$wl = explode(",", $_COOKIE["xs_compare"]);

	foreach($wl as $val)
		if(!empty($val) && $val != "undefined")
			$big_data['compare'][$val] = $val;
}