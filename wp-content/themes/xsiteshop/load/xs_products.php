<?

global $big_data;

if(empty($big_data) || count($big_data) == 0)
{
	$big_data['not_cache'] = 'y';
	include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
}


$post_data = xs_format($_POST);

if(isset($post_data['category']) && !empty($post_data['category']))
{
	$arg = array( 
		'tax_query' => array(
			array( 
				'taxonomy' => 'product_cat',
				'field' => 'id',
				'terms' => array($post_data['category'])
			),
		), 
		'posts_per_page' => xs_get_option('xs_shop_count_catalog'),
		'post_type' => 'product',
		'orderby' => xs_get_option('xs_shop_orderby_catalog'),
		'order' => xs_get_option('xs_shop_order_catalog')
	);

	if(xs_get_option('xs_shop_label_catalog'))
	{
		foreach($big_data['label_colors'] as $k => $v)
			$labels[] = $k;
			
		$arg['tax_query'][] = array( 
			'taxonomy' => 'label',
			'field' => 'id',
			'terms' => $labels
		);
		
		$arg['tax_query']['relation'] = 'AND';
	}


	$sale_products = new WP_Query($arg);
		
	if($sale_products->have_posts())
	{
		while ( $sale_products->have_posts() )
		{
			$sale_products->the_post();
			global $product;
			get_template_part('templates/product');
		}
		
		$r = 5 - ($sale_products->post_count % 5);
		
		if($r > 0 && $r < 5)
			echo str_repeat('<div class="item empty"></div>', $r);
	}
}