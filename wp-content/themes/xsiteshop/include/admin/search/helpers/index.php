<?
global $xs_data, $filter;

$filter = isset($_GET['filter']) ? xs_format($_GET['filter']) : array();

$setFilter = false;
$where = array();

if($_GET['clear'] == 'y')
{
	$post_data = '';
}

$where = array();

if(!empty($filter['date_ot']) || !empty($filter['date_do'])) // фильтр "Дата создания"
{
	if(!empty($filter['date_ot']) && !empty($filter['date_do']) && strtotime($filter['date_ot']) > strtotime($filter['date_do']))
	{
		$s = $filter['date_ot'];
		$filter['date_ot'] = $filter['date_do'];
		$filter['date_do'] = $s;
	}
	
	if(!empty($filter['date_ot']))
	{
		$where[] = "`date` >= '".date('Y-m-d', strtotime($filter['date_ot']))." 00:00:00' ";
		$setFilter = true;
	}
	
	if(!empty($filter['date_do']))
	{
		$where[] = "`date` <= '".date('Y-m-d', strtotime($filter['date_do']))." 23:59:59' ";
		$setFilter = true;
	}		
}

if(count($where) > 0)
	$where = ' WHERE '.implode(' AND ', $where);
else
	$where = '';

$xs_query = "
	SELECT 
		*,
		COUNT(*) `quantity`
	FROM 
		`xsite_search`
	".$where."
	GROUP BY
		`query`
";

$xs_data->rows = $wpdb->get_results($xs_query.get_order_limit('date', 'desc'));