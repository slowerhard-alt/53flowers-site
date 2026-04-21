<? get_header(); 

get_template_part('templates/links'); 

?><!-- Слайдер START -->
<div class="main-slider main-slider--tune">
	<div class="container"><?
		get_template_part('templates/slider'); 
	?></div>
</div>
<!-- Слайдер END --><?

// Букеты недели

$arg = array( 
	'meta_query' => [
		[
			'key' => 'top_category',
			'value' => '"409"',
			'compare' => 'LIKE'
		],
	],
	'posts_per_page' => 99,
	'post_type' => 'product',
	'meta_key' => '_price',
	'orderby' => ['meta_value_num'=>'ASC']
);	

$top_products = new WP_Query($arg);

if($top_products->have_posts())
{
	?><!-- Контентная часть START -->
	<div class="squeeze">
		<div class="container">
			<div class="squeeze__inner">
				<div class="title squeeze__title">Букеты недели!</div>
				<div class="squeeze__descr">Ниже представлены букеты этой недели. В любой букет можно внести изменения, например добавить зелень, изменить упаковку или количество цветов. Оформление букета может отличаться от представленных на сайте фотографий.</div>
				<div class="squeeze__body"><?
			
					while($top_products->have_posts())
					{
						$top_products->the_post();
						global $product;
						get_template_part('templates/product-variation');
					}
				
				?></div>
			</div>
		</div>
	</div>
	<!-- Контентная часть END --><?
}




// Композиции этой недели

$arg = array( 
	'meta_query' => [
		[
			'key' => 'top_category',
			'value' => '"410"',
			'compare' => 'LIKE'
		],
	],
	'posts_per_page' => 99,
	'post_type' => 'product',
	'meta_key' => '_price',
	'orderby' => ['meta_value_num'=>'ASC']
);	

$top_products = new WP_Query($arg);

if($top_products->have_posts())
{
	?><!-- Контентная часть START -->
	<div class="squeeze">
		<div class="container">
			<div class="squeeze__inner">
				<div class="title similar__title">Композиции этой недели</div>
				<div class="squeeze__body"><?
			
					while($top_products->have_posts())
					{
						$top_products->the_post();
						global $product;
						get_template_part('templates/product-variation');
					}
				
				?></div>
			</div>
		</div>
	</div>
	<!-- Контентная часть END --><?
}


// Популярные товары

$arg = array( 
	'meta_query' => [
		[
			'key' => 'top_category',
			'value' => '"413"',
			'compare' => 'LIKE'
		],
	], 
	'posts_per_page' => 12,
	'paged' => 1,
	'post_type' => 'product',
);	

$wp_query = new WP_Query($arg);

if($wp_query->have_posts())
{
	?><!-- Товары START -->
	<div class="goods goods-tune">
		<div class="container">
			<div class="goods__inner">
				<div class="goods__title title">Популярные товары:</div>
				<div class="goods__body slasher front_page_popular col5 goods__content-col5"></div>
			</div>
		</div>
	</div>
	<!-- Товары END --><?
}

?><div class="wrap-lay">

<!-- Похожие START --><?

$ar_terms = [];
	
$arg = array( 
	'tax_query' => array(
		[
			'taxonomy' => 'product_tag',
			'field' => 'id',
			'terms' => 328,
			'operator' => 'NOT IN'
		],
		'relation' => 'AND'
	), 
	'meta_query' => [
		[
			'key' => 'top_category',
			'value' => '"412"',
			'compare' => 'LIKE'
		],
	],
	'posts_per_page' => -1,
	'post_type' => 'product',
	'meta_key' => '_price',
	'orderby'  => ['meta_value_num'=>'ASC'],
);	

$sale_products = new WP_Query($arg);

if($sale_products->have_posts())
{
	while($sale_products->have_posts())
	{
		$sale_products->the_post();
		global $product;
		
		/*
		$terms = get_the_terms($product->get_id(), 'product_cat');
		
		foreach($terms as $v)
			$ar_terms[$v->term_id][] = $product;
		*/
			
		if($term_id = get_post_meta($product->get_id(), '_yoast_wpseo_primary_product_cat', true))
			$ar_terms[$term_id][] = $product;
	}
}



if(count($ar_terms) > 0)
{
	$terms = get_terms( [
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
	] );

	foreach($terms as $k => $v)
	{
		if(isset($ar_terms[$v->term_id]))
		{
			?><div class="similar similar--tune slasher similar__product">
				<div class="container"><?
					?><div class="similar__title title"><?
						
						if($title = get_term_meta($v->term_id, 'home_title', true))
							echo $title;
						else
							echo $big_data['categories'][$v->term_id]->name;
						
					?></div>
					<div class="similar__inner similar__inner--slider"><?
					
						foreach($ar_terms[$v->term_id] as $product)
						{
							global $product;
							get_template_part('templates/product');
						}
						
					?></div>
					<div class="similar__button">
						<a class="btn similar__btn btn-green" href="<?=get_category_link($v->term_id)?>">Посмотреть все <?=mb_strtolower($big_data['categories'][$k]->name, 'utf-8') ?></a>
					</div><?
				?></div>
			</div><?
		}
	}
}

?><!-- Похожие END -->

</div>



<? get_footer();