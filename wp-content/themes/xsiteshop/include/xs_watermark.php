<? // Водиной знак

function xs_watermark($original) 
{ 
	$quality = 70;
	$max_width = 900;
	
	$placement = 'middle,center';
	
	$watermark = $_SERVER['DOCUMENT_ROOT']."/wp-content/themes/xsiteshop/images/watermark.png";
	
	$original = urldecode(str_replace(
			$_SERVER['HTTP_X_FORWARDED_PROTO']."://".$_SERVER['HTTP_HOST'],
			$_SERVER['DOCUMENT_ROOT'],
			$original
		)
	);

	$name = str_replace(
		array($_SERVER['DOCUMENT_ROOT'], "/wp-content/uploads/xs_cache/resize_img/", "/"), 
		array("", "", "-"), 
		$original
	);
	
	$original_new = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/xs_cache/watermark/".$name;
	

	$info_o = @getImageSize($original); 
	if (!$info_o) 
		return false; 

	if(file_exists($original_new))
		return str_replace($_SERVER['DOCUMENT_ROOT'], "", $original_new); 
	else 
	{
		 
		if( ($info_o[0] > 250) && ($info_o[1] > 250) )
		{
			
			if($info_o[0] > $max_width || $info_o[1] > $max_width){
				if($info_o[0] > $info_o[1]){
					$ratio = $info_o[0] / $info_o[1];
					$info_o_xs[0] = $max_width;
					$info_o_xs[1] = $info_o_xs[0] / $ratio;
				} else {
					$ratio = $info_o[1] / $info_o[0];
					$info_o_xs[1] = $max_width;
					$info_o_xs[0] = $info_o_xs[1] / $ratio;
				}
			} else {
				$info_o_xs[1] = $info_o[1];
				$info_o_xs[0] = $info_o[0];
			}

			$info_w = @getImageSize($watermark); 
			if (!$info_w) 
				return false; 

			list ($vertical, $horizontal) = explode(',', $placement); 
			list($vertical, $sy) = explode('=', trim($vertical)); 
			list($horizontal, $sx) = explode('=', trim($horizontal)); 

			$info_w[0] = $info_o_xs[0] * 0.35;
			$info_w[1] = $info_w[0] / 3.38983051;


			switch (trim($vertical)) { 
			  case 'bottom': 
				 $y = $info_o_xs[1] - $info_w[1] - (int)$sy; 
				 break; 
			  case 'middle': 
				 $y = ceil($info_o_xs[1]/2) - ceil($info_w[1]/2) + (int)$sy; 
				 break; 
			  default: 
				 $y = (int)$sy; 
				 break; 
			} 

			switch (trim($horizontal)) { 
			  case 'right': 
				 $x = $info_o_xs[0] - $info_w[0] - (int)$sx; 
				 break; 
			  case 'center': 
				 $x = ceil($info_o_xs[0]/2) - ceil($info_w[0]/2) + (int)$sx; 
				 break; 
			  default: 
				 $x = (int)$sx; 
				 break; 
			} 
			
			$original = @imageCreateFromString(file_get_contents($original)); 
			$watermark = @imageCreateFromString(file_get_contents($watermark)); 
			$out = imageCreateTrueColor($info_o_xs[0],$info_o_xs[1]); 
			$watermark_out = imageCreateTrueColor($info_w[0],$info_w[1]);
		   
			imagecopyresized($out, $original, 0, 0, 0, 0, $info_o_xs[0], $info_o_xs[1], $info_o[0], $info_o[1]); 
			imagecopyresized($out, $watermark, $x, $y, 0, 0, $info_w[0], $info_w[1], 400, 118);
		     
			$xs_result = imageJPEG($out, $original_new, $quality); 

			imageDestroy($out); 
			imageDestroy($original); 
			imageDestroy($watermark); 
			
			return str_replace($_SERVER['DOCUMENT_ROOT'], "", $original_new);
		} 
		else
			return str_replace($_SERVER['DOCUMENT_ROOT'], "", $original); 
	}
} ?>