<?

add_filter('posts_results', 'xs_search_cir_lat', 10, 2);

function xs_search_cir_lat($posts, $query) 
{
	if(is_admin() || !$query->is_search) return $posts;
	
	global $wp_query;

	if ($wp_query->found_posts == 0) 
	{	
		// замена латиницы на кириллицу
		$letters = array( 'f' => 'а', ',' => 'б', 'd' => 'в', 'u' => 'г', 'l' => 'д', 't' => 'е', '`' => 'ё', ';' => 'ж', 'p' => 'з', 'b' => 'и', 'q' => 'й', 'r' => 'к', 'k' => 'л', 'v' => 'м', 'y' => 'н', 'j' => 'о', 'g' => 'п', 'h' => 'р', 'c' => 'с', 'n' => 'т', 'e' => 'у', 'a' => 'ф', '[' => 'х', 'w' => 'ц', 'x' => 'ч', 'i' => 'ш', 'o' => 'щ', ']' => 'ъ', 's' => 'ы', 'm' => 'ь', '\'' => 'э', '.' => 'ю', 'z' => 'я' );
		
		$cir = array( 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я' );
		$lat = array( 'f', ',', 'd', 'u', 'l', 't', '`', ';', 'p', 'b', 'q', 'r', 'k', 'v', 'y', 'j', 'g', 'h', 'c', 'n', 'e', 'a', '[', 'w', 'x', 'i', 'o', ']', 's', 'm', '\'', '.', 'z', 'F', ',', 'D', 'U', 'L', 'T', '`', ';', 'P', 'B', 'Q', 'R', 'K', 'V', 'Y', 'J', 'G', 'H', 'C', 'N', 'E', 'A', '[', 'W', 'X', 'I', 'O', ']', 'S', 'M', '\'', '.', 'Z' );
		$new_search = str_replace( $lat, $cir, $wp_query->query_vars['s'] );
		
		// производим выборку из базы данных
		
		global $wpdb;
		
		$request = $wpdb->get_results(str_replace($wp_query->query_vars['s'], $new_search, $query->request));
		
		if(!$request)
			$request = $wpdb->get_results("SELECT 
					* 
				FROM 
					".$wpdb->prefix."posts AS p 
				LEFT JOIN ".$wpdb->prefix."postmeta AS pm ON pm.post_id = p.ID AND pm.meta_key = '_sku'
				WHERE 
					pm.meta_value = '".$wp_query->query_vars['s']."'
			");
		
		if($request) 
		{
			$new_posts = array();
			foreach($request as $post) 
				$new_posts[] = get_post($post->ID);
			if(count($new_posts) > 0)
				$posts = $new_posts;
		}
	}
	// возвращаем массив найденных постов
	return $posts;
}


// ajax поиск по сайту

add_action("wp_ajax_nopriv_ajax_search", "ajax_search");
add_action("wp_ajax_ajax_search", "ajax_search");

function ajax_search()
{
	$q = xs_format($_POST["term"]);
	
    $args = array(
        "post_type"      => "product",
        "post_status"    => "publish",
        "s"              => $q,
        "posts_per_page" => 15
    );
	
    $query = new WP_Query($args); 
    
	if($query->have_posts()) 
	{
        while($query->have_posts())
		{
			$query->the_post();
			
			global $product;
						
			?><li class="header__search_item">
				<a class="header__search_item-link" href="<? the_permalink() ?>">
					<span class="header__search_item-image"><?
					
						$src = wp_get_attachment_image_src($product->image_id, 'thumbnail');
						$src = xs_img_resize($src[0], 100, 100);

						?><img class="header__search_item-img" src="<? echo $src ?>" alt="<?=htmlspecialchars($product->name) ?>" width="100" height="100">
					</span>
					<span class="header__search_item-cont">
						<span class="header__search_item-name"><?=$product->get_name() ?></span>
						<span class="header__search_item-price"><?=$product->get_price_html() ?></span>
					</div>
				</a>
			</li><?
        }
		
		?><li class="header__search_item header__search_item--all">Показать все результаты</li><?
    }
	else 
	{
        ?><li class="header__search_item header__search_item--empty">Ничего не найдено</li><?
    }
 		
	if(!is_user_logged_in())
	{
		// Записываем данные
	
		global $wpdb;
		
		$q = $wpdb->_real_escape($q);
		
		$wpdb->query("INSERT INTO `xsite_search` SET `query` = '".$q."'");
	}
   
	die();
}