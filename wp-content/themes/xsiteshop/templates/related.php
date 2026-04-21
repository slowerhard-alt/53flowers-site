<?
global $big_data, $product;
$count_related = 12;

$upsells = $product->get_upsells();
$cross_sells = $product->get_cross_sell_ids();
$related = $product->get_related($count_related);
$product_id = $product->id;


if(!isset($big_data['block_active']) || !is_array($big_data['block_active']) || !isset($big_data['block_active']['adding']) || $big_data['block_active']['adding'] != 'y')
{ 
	// Так же вы можете добавить
	
	$added_category_id = isset($_POST['added_category_id']) 
		? (int)$_POST['added_category_id'] 
		: 271;
	
	$ar_tax = array(
		array( 
			'taxonomy' => 'product_tag',
			'field' => 'id',
			'terms' => 309,
			'post__not_in' => [$product_id]
		)
	);
		
	$args = array( 
		'tax_query' => $ar_tax,
		'posts_per_page' => 99,
		'post_type' => 'product',
	);	


	if(is_super_admin())
		$args['post_status'] = $big_data['admin_product_status'];


	$related_tag_products = new WP_Query($args);

	if($related_tag_products->have_posts())
	{
		$ar_products = [];
		$ar_cats = [];
		
		while($related_tag_products->have_posts())
		{
			$related_tag_products->the_post();
			global $product;
			$terms = get_the_terms($product->ID, 'product_cat');
			$product->terms = $terms;
						
			if($terms)
			{
				foreach($terms as $v)
				{
					if($v->term_id != $big_data['present_term_id'])
					{
						$ar_cats[] = $v->term_id;
						$ar_products[$v->term_id][] = $product;
					}
				}
			}
		}
		
		?><div class="similar sim-product similar--tune similar__product sim-last similar--added">
			<div class="similar__title title">
				Так же вы можете добавить:
			</div><?
			
			$ar_cats = array_unique($ar_cats);
			
			if(count($ar_cats) > 0)
			{
				?><div class="subcategories-list subcategories-list--added"><?
					?><div class="subcategories-list__container"><?
					
						foreach($big_data['categories'] as $v)
						{
							if(in_array($v->term_id, $ar_cats))
							{
								?><span class="subcategories-list__item<?=$added_category_id == $v->term_id ? ' subcategories-list__item--active' : '' ?>" data-id="<?=$v->term_id ?>"><?=$v->name ?></span><?
							}
						}
					
					?></div><?
				?></div><?
			}
			
			?><div class="similar__inner--added"><?
				?><div class="similar__inner similar__inner--slider similar__inner-extend"><?

					foreach($ar_products[$added_category_id] as $product)
					{
						global $product;
						get_template_part('templates/product');
					}
					
				?></div><?
			?></div><?
		?></div><?
	}
}


if(!isset($big_data['block_active']) || !is_array($big_data['block_active']) || !isset($big_data['block_active']['similar']) || $big_data['block_active']['similar'] != 'y')
{ 
	// Похожие товары

	if(sizeof($related) > 0)
	{
		$args = apply_filters('woocommerce_related_products_args', array(
			'post_type'            => 'product',
			'ignore_sticky_posts'  => 1,
			'no_found_rows'        => 1,
			'posts_per_page'       => $count_related,
			'orderby'              => 'rand',
			'post__in'             => $related,
			'post__not_in'         => array($product_id),
			'tax_query' => array(
				[
					'taxonomy' => 'product_tag',
					'field' => 'id',
					'terms' => 328,
					'operator' => 'NOT IN'
				],
			), 
		));


		if(is_super_admin())
			$args['post_status'] = $big_data['admin_product_status'];


		$related_products = new WP_Query($args);

		if($related_products->have_posts())
		{
			?><div class="similar sim-product similar--tune similar__product sim-last">
				<!-- <div class="container"> -->
				<div class="similar__title title">
					Похожие товары
				</div>
				<div class="similar__inner similar__inner--slider"><?
		
					while($related_products->have_posts())
					{
						$related_products->the_post();
						global $product;
						get_template_part('templates/product');
					}
					
				?></div><?
				?><!-- </div> --><?
			?></div><?
		}

		wp_reset_postdata();
	}
}


// Кросселы (сопутствующие)

if(sizeof($cross_sells) > 0)
{
	$args = array(
		'post_type'            => 'product',
		'ignore_sticky_posts'  => 1,
		'no_found_rows'        => 1,
		'orderby'              => 'rand',
		'post__in'             => $cross_sells,
		'tax_query' => array(
			[
				'taxonomy' => 'product_tag',
				'field' => 'id',
				'terms' => 328,
				'operator' => 'NOT IN'
			],
		), 
	);


	if(is_super_admin())
		$args['post_status'] = $big_data['admin_product_status'];


	$cross_sells_products = new WP_Query($args);

	if($cross_sells_products->have_posts())
	{
		?><div class="similar sim-product similar--tune similar__product sim-last">
			<!-- <div class="container"> -->
			<div class="similar__title title">
				Кросселы:
			</div>
			<div class="similar__inner similar__inner--slider"><?
	
				while($cross_sells_products->have_posts())
				{
					$cross_sells_products->the_post();
					global $product;
					get_template_part('templates/product');
				}
				
			?></div><?
			?><!-- </div> --><?
		?></div><?
	}
	wp_reset_postdata();
}


// Апсейлы (так же могут заинтересовать)

if(sizeof($upsells) > 0)
{
	$args = apply_filters('woocommerce_upsell_display_args', array(
		'post_type'            => 'product',
		'ignore_sticky_posts'  => 1,
		'no_found_rows'        => 1,
		'orderby'              => 'rand',
		'post__in'             => $upsells,
		'tax_query' => array(
			[
				'taxonomy' => 'product_tag',
				'field' => 'id',
				'terms' => 328,
				'operator' => 'NOT IN'
			],
		), 
	));


	if(is_super_admin())
		$args['post_status'] = $big_data['admin_product_status'];


	$upsells_products = new WP_Query($args);

	if($upsells_products->have_posts())
	{

		?><div class="similar sim-product similar--tune similar__product sim-last">
			<!-- <div class="container"> -->
			<div class="similar__title title">
				Апсейлы:
			</div>
			<div class="similar__inner similar__inner--slider"><?
	
				while($upsells_products->have_posts())
				{
					$upsells_products->the_post();
					global $product;
					get_template_part('templates/product');
				}
				
			?></div><?
			?><!-- </div> --><?
		?></div><?
	}

	wp_reset_postdata();
}

wp_reset_postdata();
