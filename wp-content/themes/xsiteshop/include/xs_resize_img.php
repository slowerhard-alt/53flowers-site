<?

// Сжатие изображений перед выводом

function xs_img_resize($src, $width, $height, $position = 'cover', $rgb=0xFFFFFF, $quality=80)
{
	$src = str_replace(esc_url( home_url( '/' ) ), "/", $src);
	
	$ex = explode("/", $src);
	$name = $ex[count($ex)-1];
	
	if(isset($ex[count($ex)-2]))
		$name = $ex[count($ex)-2]."_".$name;
	
	if(isset($ex[count($ex)-3]))
		$name = $ex[count($ex)-3]."_".$name;
	
	$prefix = $position != 'cover' ? $position."-" : '';
			
	if(mb_strpos($name, '.', 0, 'utf-8') === false)
		$a = str_replace("/", "-", $name)."-".$prefix.$width."x".$height;
	else
		$a = str_replace("/", "-", str_replace(".", "-".$prefix.$width."x".$height.".", $name));
	
	$dest = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/xs_cache/resize_img/".$a;

	if(!is_file($dest))
	{
		$src = $_SERVER['DOCUMENT_ROOT'].$src;
	 
		$size = getimagesize($src);
	 
		if ($size === false) return false;
	 
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		$icfunc = "imagecreatefrom" . $format;
		//$imagefunc = "image" . $format;
		if (!function_exists($icfunc)) return false;
		
		//if(!function_exists($imagefunc))
		$imagefunc = "imagejpeg"; 
		
		if($position == 'contain')
		{
			if($size[0] > $width || $size[1] > $height)
			{
			 
				if($size[0] >= $size[1]) // ширина больше высоты
				{			 
					$ratio = $size[1] / $size[0];
					$new_width   = $width;
					$new_height  = floor($width * $ratio);
					
					if($new_height > $height)
					{
						$ratio = $size[0] / $size[1];
						$new_height = $height;
						$new_width   = floor($height * $ratio);
					}
				} 
				else 
				{
					$ratio = $size[0] / $size[1];
					$new_width   = floor($height * $ratio);
					$new_height  = $height;
					
					if($new_width > $width)
					{
						$ratio = $size[1] / $size[0];
						$new_width = $width;
						$new_height  = floor($width * $ratio);
					}
				}
				
				//echo $size[0];
				//echo "_".$size[1];
				//echo "_".$new_width;
				//echo "_".$new_height;
				//die();
				
				$isrc = $icfunc($src);
				$idest = imagecreatetruecolor($new_width, $new_height);
			 
				imagefill($idest, 0, 0, $rgb);
				imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
				
				$imagefunc($idest, $dest, $quality);
			 
				imagedestroy($isrc);
				imagedestroy($idest);
			}
			else
				$dest = $src;
		}
		else // cover
		{
			if($size[0] > $width || $size[1] > $height)
			{
				$x_ratio = $width / $size[0];
				$y_ratio = $height / $size[1];
			 
				$ratio       = min($x_ratio, $y_ratio);
				$use_x_ratio = ($x_ratio == $ratio);
			 
				$new_width   = $use_x_ratio  ? floor($size[0] * $y_ratio) : $width;
				$new_height  = !$use_x_ratio ? floor($size[1] * $x_ratio) : $height;
				$new_left    = $use_x_ratio  ? floor(($width - $new_width) / 2) : 0;
				$new_top     = !$use_x_ratio ? floor(($height - $new_height) / 2) : 0;
			 
				$isrc = $icfunc($src);
				$idest = imagecreatetruecolor($width, $height);
			 
				imagefill($idest, 0, 0, $rgb);
				imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);
			 
				$imagefunc($idest, $dest, $quality);
			 
				imagedestroy($isrc);
				imagedestroy($idest);
			}
			else
				$dest = $src;
		}
	}
	return str_replace($_SERVER['DOCUMENT_ROOT'], "", $dest);
}
