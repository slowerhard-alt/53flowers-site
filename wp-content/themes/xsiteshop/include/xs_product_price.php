<?

// Переопределяем вывод цены

function hide_all_wc_prices() 
{
  return '';
}

add_filter('woocommerce_get_price_html', 'hide_all_wc_prices');
add_filter('woocommerce_get_price_html', 'custom_price_html', 100, 2);

function get_product_prices($product)
{
	$ar_prices = [
		'is_from' => false,
		'from' => 0,
		'to' => 0,
		'sale' => 0,
		'percent' => 0,
	];
	
	$_from = false;
	$_ar_prices = [];
	
    if($product->price > 0 || $product->product_type == 'variable')
	{
		$to = isset($product->price) && is_numeric($product->price)
			? $product->price
			: 0;
	  
        if(($product->price && isset($product->regular_price)) || $product->product_type == 'variable') 
		{
			$p = 0;
			$ar_p = [];
			
			if($product->product_type == 'variable')
			{
				foreach($product->get_available_variations() as $v)
				{
					if($v['is_in_stock'] != 1)
						continue;
					
					$_ar_prices[] = $v['display_price'];
					
					if(isset($v['display_regular_price']) && $v['display_regular_price'] > $v['display_price'])
						$ar_p[] = round(100 - ($v['display_price'] / $v['display_regular_price'] * 100));
					else
						$ar_p[] = 0;
				}
			
				if(count($ar_p))
				{
					$ar_p = array_unique($ar_p);
					
					if(count($ar_p) == 1)
						$p =  $ar_p[0] < 5 ? 0 : "-".$ar_p[0]."%";
					else
						$p = max($ar_p) < 5 ? 0 : "скидка до ".max($ar_p)."%";
				}
				
				$_ar_prices = array_unique($_ar_prices);
				
				if(count($_ar_prices) > 1)
					$_from = true;
				
				$from = count($_ar_prices) > 0
					? min($_ar_prices)
					: 0;
			} 
			else 
				$from = $product->regular_price;
		
			if($_from)
			{
				$ar_prices = [
					'is_from' => true,
					'from' => $from,
					'to' => 0,
					'sale' => 0,
					'percent' => $p,
				];
			}
			elseif($to != $from)
			{
				$p = $from > 0
					? round(100 - ($to / $from * 100))
					: 0;
				
				$ar_prices = [
					'is_from' => false,
					'from' => $from,
					'to' => $to,
					'sale' => $to - $from,
					'percent' => $p < 5 ? 0 : "-".$p."%",
				];
			}
			else
				$ar_prices = [
					'is_from' => false,
					'from' => 0,
					'to' => $to,
					'sale' => 0,
					'percent' => $p,
				];
		} 
		else
			$ar_prices = [
				'is_from' => false,
				'from' => 0,
				'to' => $to,
				'sale' => 0,
				'percent' => $p,
			];		
	} 
   
	return $ar_prices;
}

function custom_price_html($price, $product)
{
	$ar_prices = get_product_prices($product);
	$price = "";
	
    if($ar_prices['from'] != 0 || $ar_prices['to'] != 0)
	{
		$to = $product->price;
		
		if($ar_prices['is_from'])
			$price = '<span class="valid">от '.((is_numeric($ar_prices['from'])) ? wc_price($ar_prices['from']) : $ar_prices['from']).'</span> ';
		elseif($ar_prices['to'] > 0 && $ar_prices['from'] > 0 && $ar_prices['to'] != $ar_prices['from'])
		{
			$price .= '<span class="expire">'.((is_numeric($ar_prices['from'])) ? wc_price($ar_prices['from']) : $ar_prices['from']).'</span> ';
			$price .= '<span class="valid">'.((is_numeric($ar_prices['to'])) ? wc_price($ar_prices['to']) : $ar_prices['to']).'</span>';
		}
		else
			$price .= '<span class="valid">'.((is_numeric($ar_prices['to'])) ? wc_price($ar_prices['to']) : $ar_prices['to']).'</span>';

        if($unit = get_post_meta($product->id, 'xs_unit', true)) 
    		$price .= " ".$unit;
		
	} 
	else
	   $price .= 'цена не указана';
   
	return apply_filters('woocommerce_get_price', $price);
}