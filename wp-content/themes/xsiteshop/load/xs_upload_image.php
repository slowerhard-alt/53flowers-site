<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
global $big_data, $wpdb;

$data = array();
 
$good_expansion = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
 
if( isset( $_GET['uploadfiles'] ) )
{
    $error = false; 
    $files = array();
 
	$path = $big_data['component_image_path'];
    $uploaddir = $_SERVER['DOCUMENT_ROOT'].$path;
 
	$data = array();
	if (is_dir($uploaddir)) 
	{
	   if ($dh = opendir($uploaddir)) { 
		   while (($file = readdir($dh)) !== false)
				if (!empty($file) && $file != '.' && $file != '..' )
				{
					$tmp = explode('.', $file);
					$data[$tmp[0]] = $file;
				}
		   closedir($dh); 
	   }
	}

    foreach( $_FILES as $file )
	{
		$expansion = explode('.', $file['name']);
		$filename = get_hash((int)$_GET['component_id']);
		
		$expansion = mb_strtolower($expansion[count($expansion) - 1], 'utf-8');
		
		if(in_array($expansion, $good_expansion))
		{
			if(isset($data[$filename]))
			{
				for($i = 1; $i < 999999; $i++)
				{
					$tmp_name = $filename."_".$i;
					if(!isset($data[$tmp_name]))
					{
						$filename = $tmp_name;
						break;
					}
				}
			}
			
			if( move_uploaded_file( $file['tmp_name'], $uploaddir.$filename.".".$expansion ) )
			{
				$files[] = $path.$filename.".".$expansion;
				
				$resize_img = xs_img_resize($path.$filename.".".$expansion, 250, 150);
				
				if($resize_img != $path.$filename.".".$expansion)
				{
					if (copy($_SERVER['DOCUMENT_ROOT'].$resize_img, $uploaddir.$filename.".".$expansion))
						unlink($_SERVER['DOCUMENT_ROOT'].$resize_img);
				}
				
				if((int)$_GET['component_id'] > 0)
					$wpdb->query("UPDATE `xsite_store_components` SET `image` = '".$filename.".".$expansion."' WHERE `id` = '".(int)$_GET['component_id']."'");
			}
			else
				$error = true;
		}
    }
 
    $data = $error ? array('error' => 'Ошибка загрузки изображения.') : array('files' => $files );
 
    echo json_encode( $data );
}