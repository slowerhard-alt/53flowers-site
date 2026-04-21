<?
if(!is_admin())
{
	$badcount = 0;
	$badops = array("UNION",
		"OUTFILE",
		"FROM",
		"CREATE",
		"SELECT",
		"WHERE",
		"SHUTDOWN",
		"UPDATE ",
		"UPDATE%20",
		"DELETE",
		"CHANGE",
		"MODIFY",
		"RENAME",
		"RELOAD",
		"ALTER",
		"GRANT",
		"DROP",
		"INSERT",
		"CONCAT",
		"cmd",
		"exec",
		"<[^>]*body*\"?[^>]*>",
		"<[^>]*script*\"?[^>]*>",
		"<[^>]*object*\"?[^>]*>",
		"<[^>]*iframe*\"?[^>]*>",
		"<[^>]*img*\"?[^>]*>",
		"<[^>]*frame*\"?[^>]*>",
		"<[^>]*applet*\"?[^>]*>",
		"<[^>]*meta*\"?[^>]*>",
		"<[^>]*style*\"?[^>]*>",
		"<[^>]*form*\"?[^>]*>",
		"<[^>]*div*\"?[^>]*>"
	);

	foreach ($_REQUEST as $params => $inputdata) 
	{
		for ($i = 0; $i < sizeof($badops); $i++) 
		{
			if (is_string($inputdata) && preg_match('/'.$badops[$i].'/i',$inputdata)) 
			{
				 $badcount = 1;
			}
		}
	}
	if(isset($_GET['add-to-cart']) && !is_numeric($_GET['add-to-cart']))
		$badcount = 1;
	 
	if( $badcount  )
	{
		header("HTTP/1.0 404 Not Found");
		die(':(');
	}
}
