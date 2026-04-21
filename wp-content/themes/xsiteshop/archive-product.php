<?
if(!defined('ABSPATH')) exit;

if(!isset($_GET['orderby']))
	$_GET['orderby'] = isset($_SESSION['xs_catalog_orderby']) && !empty($_SESSION['xs_catalog_orderby']) ? $_SESSION['xs_catalog_orderby'] : 'price';

global $big_data;

$category = get_queried_object();

if(
	get_term_meta($category->term_id, "is_hide", true) == 'y' ||
	(
		$category->term_id == $big_data['flowers_instock_term_id'] &&
		!is_user_logged_in()
	)
)
{
	global $post, $page, $wp_query;
	$wp_query->set_404();
	header("HTTP/1.1 404 Not Found");
	get_template_part('404');
	die();
}
elseif(
	$category->term_id == $big_data['flowers_instock_term_id'] &&
	!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
)
{
	// Обновляем товары для категории Букеты в наличии
	
	global $wpdb;
	
	$cat_ids = [12];
				
	$subcategories = get_store_subcategories_all(12);
		foreach($subcategories as $v)
			$cat_ids[] = $v->id;
	
	$db_products = $wpdb->get_results("
		SELECT 
			 pc.`product_id`,
			 p.`post_parent`,
			 c.`ms_quantity`,
			 pc.`quantity`
		FROM 
			`xsite_store_products` pc
		INNER JOIN `xsite_store_components` c ON pc.`component_id` = c.`id`
		INNER JOIN `xsite_posts` p ON pc.`product_id` = p.`ID`
		WHERE 
			c.`category_id` IN ('".implode("','", $cat_ids)."') AND
			pc.`site` = '53flowers' AND
			p.`post_type` IN ('product', 'product_variation')
	");
	
	$prod_ids = [];
	$ar_prods = [];
	
	foreach($db_products as $v)
		$prod_ids[$v->product_id] = (object)[
			'product_id' => $v->product_id,
			'post_parent' => $v->post_parent
		];
	
	foreach($prod_ids as $id => $prod)
	{
		$is_instock = true;
		
		foreach($db_products as $v)
		{
			if($v->product_id != $id)
				continue;
			
			if($v->ms_quantity < $v->quantity)
				$is_instock = false;
		}
		
		if($is_instock)
			$ar_prods[] = $prod->post_parent == 0
			? $prod->product_id
			: $prod->post_parent;
	}
	
	$ar_prods = array_unique($ar_prods);
	
	$wpdb->query("DELETE FROM `xsite_postmeta` WHERE `meta_key` = '_is_flowers_instock' AND `post_id` NOT IN ('".implode("','", $ar_prods)."')");
	
	foreach($ar_prods as $v)
		update_post_meta($v, '_is_flowers_instock', 'y');
	
	//wp_redirect(get_term_link($big_data['flowers_instock_term_id'], 'product_cat')."?is_upd=false")
}

get_header();

$cat_description = explode("<!--more-->", $category->description);
$show_subcat = get_term_meta($category->term_id, "display_type", true);
$is_hide_related = get_term_meta($category->term_id, "is_hide_related", true);
$is_present = $category->term_id == $big_data['present_term_id'] || $category->parent == $big_data['present_term_id'];
$is_prazdnik = $category->term_id == $big_data['prazdnik_term_id'];
$is_14_fevralya = $category->term_id == $big_data['14_fevralya_term_id'];
$is_den_materi = $category->term_id == $big_data['den_materi_term_id'];
$ar_subcat_show = [243, 264, 233];
$is_hide_filter = in_array($category->term_id, [$big_data['special_term_id'], 413]);

if(empty($show_subcat))
	$show_subcat = get_option('woocommerce_category_archive_display');

if(is_shop() && !is_search())
	$show_subcat = 'subcategories';

?><div class="catalog_head"><?
	?><h1><?=($h1 = get_term_meta($category->term_id, 'h1', true)) ? htmlspecialchars_decode($h1) : woocommerce_page_title(false); ?></h1><?
?></div><?


?><div class="category_description"><?

	if(isset($cat_description[1]) && !empty($cat_description[0]))
	{
		?><div class="category_description__text"><? 
			
			echo wpautop($cat_description[0]);
			unset($cat_description[0]);
			
		?></div><?
	}


	$subcat = [];
	
	if(in_array($category->term_id, $ar_subcat_show) || in_array($category->parent, $ar_subcat_show))
	{
		$parent_cat_id = (in_array($category->term_id, $ar_subcat_show)) ? $category->term_id : $category->parent;
		
		$present_cat = $big_data['categories'][$parent_cat_id];
		
		$present_cat->name = "Все ".mb_strtolower($present_cat->name, 'utf-8');
		
		$subcat[] = $present_cat;
		
		if(in_array($category->term_id, $ar_subcat_show))
		{
			foreach($big_data['categories'] as $v)
				if($v->parent == $category->term_id)
					$subcat[] = $v;
		}
		else
		{
			foreach($big_data['categories'] as $v)
				if($v->parent == $category->term_id || $v->parent == $category->parent)
					$subcat[] = $v;
		}
		
		if(count($subcat) == 1)
			$subcat = [];
	}
	else
	{
		foreach($big_data['categories'] as $v)
			if($v->parent == $category->term_id)
				$subcat[] = $v;
	}
	
	
	// Выводим подкатегории крупно
	
	if($show_subcat == 'subcategories')
	{
		if(count($subcat) > 0)
		{
			?><div class="subcategories"><?
			
			foreach($subcat as $v)
			{
				if(!$src = wp_get_attachment_image_src(get_woocommerce_term_meta($v->term_id, 'thumbnail_id', true), "shop_catalog"))
					$src = get_bloginfo('template_url')."/images/pics/noimage_news.gif";
				else
					$src = $src[0];
		
				$src = xs_img_resize($src, 274, 274);
					
				?><div class="subcategories__item"><?
				
					?><div class="subcategories__container"><?

						if(is_super_admin())
						{
							?><a target="_blank" href="/wp-admin/term.php?taxonomy=<?=$v->taxonomy ?>&tag_ID=<?=$v->term_id ?>&post_type=product" class="xs_link_edit"></a><? 
						}
						
						?><a class="subcategories__link" href="<?=get_term_link($v->term_id, 'product_cat') ?>"><?
							?><span class="subcategories__image"><?
								?><img class="subcategories__img" width="274" height="274" src="<? echo xs_img_resize($src, 274, 274) ?>" alt="<?=htmlspecialchars($v->name) ?>" loading="lazy"><?
							?></span><?
							
							?><span class="subcategories__name"><?=$v->name ?></span><?
						?></a><?
						
					?></div><?
				?></div><?
			}
			
			?></div><?
		}
	}
	else // Список товаров
	{	
		if($category->term_id != 413)
		{
			$arg = array( 
				'meta_query' => [
					[
						'key' => 'top_category',
						'value' => '"'.$category->term_id.'"',
						'compare' => 'LIKE'
					],
				],
				'posts_per_page' => 99,
				'post_type' => 'product',
				'meta_key' => '_price',
				'orderby' => ['meta_value_num'=>'ASC']
			);

			if(is_super_admin())
				$arg['post_status'] = $big_data['admin_product_status'];
			

			$top_products = new WP_Query($arg);
			$top = get_term_meta($category->term_id, 'top', true);
			
			if($top_products->have_posts())
			{
				?><!-- Контентная часть START -->
				<div class="squeeze"><?
				
					if($top && $top == 'p')
					{
						?><div class="goods__content-col5"><?
						
							while($top_products->have_posts())
							{
								$top_products->the_post();
								global $product;
								get_template_part('templates/product');
								
								$big_data['hide_product'][] = $product->get_id();
							}
							
						?></div><?
					}
					else
					{
						?><div class="squeeze__inner">
							<div class="squeeze__body"><?
						
								while($top_products->have_posts())
								{
									$top_products->the_post();
									global $product;
									get_template_part('templates/product-variation');
									
									$big_data['hide_product'][] = $product->get_id();
								}
							
							?></div>
						</div><?
					}
					
				?></div>
				<!-- Контентная часть END --><?
			}
		}
		
		// Композиции этой недели для праздников
		
		if($is_prazdnik || $is_14_fevralya || $is_den_materi)
		{
			$tag_id = 392;
			
			if($is_14_fevralya)
				$tag_id = 435;
			elseif($is_den_materi)
				$tag_id = 438;
				
			$arg = array( 
				'tax_query' => array(
					[
						'taxonomy' => 'product_tag',
						'field' => 'id',
						'terms' => $tag_id
					],
				),
				'posts_per_page' => 99,
				'post_type' => 'product',
				'meta_key' => '_price',
				'orderby' => ['meta_value_num'=>'ASC']
			);	


			if(is_super_admin())
				$arg['post_status'] = $big_data['admin_product_status'];


			$top_products = new WP_Query($arg);
			
			$top = get_term_meta($category->term_id, 'top', true);
			
			if($top_products->have_posts())
			{
				if($h3 = get_term_meta($category->term_id, 'h3', true))
				{
					?><div class="catalog_head"><?
						?><h2><?=$h3 ?></h2><?
					?></div><?
				}
				
				?><div class="squeeze"><?
				
					if($top && $top == 'p')
					{
						?><div class="goods__content-col5"><?
						
							while($top_products->have_posts())
							{
								$top_products->the_post();
								global $product;
								get_template_part('templates/product');
								
								$big_data['hide_product'][] = $product->get_id();
							}
							
						?></div><?
					}
					else
					{
						?><div class="squeeze__inner">
							<div class="squeeze__body"><?
						
								while($top_products->have_posts())
								{
									$top_products->the_post();
									global $product;
									get_template_part('templates/product-variation');
									
									$big_data['hide_product'][] = $product->get_id();
								}
							
							?></div>
						</div><?
					}
					
				?></div><?
			}
		}
		
		// Композиции этой недели для праздников -- конец -- 
		

		// Композиции этой недели для спецпредложений
		
		if($category->term_id == $big_data['special_term_id'])
		{
			$arg = array( 
				'meta_query' => [
					[
						'key' => 'top_category',
						'value' => '"415"',
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
		}
		
		// Композиции этой недели для спецпредложений -- конец -- 
		

		// Композиции этой недели для раздела Маме
		
		if($category->term_id == $big_data['mother_term_id'])
		{
			$arg = array( 
				'meta_query' => [
					[
						'key' => 'top_category',
						'value' => '"420"',
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
		}
		
		// Композиции этой недели для раздела Маме -- конец -- 
			
		
		if($h2 = get_term_meta($category->term_id, 'h2', true))
		{
			?><div class="catalog_head"><?
				?><h2><?=$h2; ?></h2><?
			?></div><?
		}

		?><div class="xcategory">
			<div class="xcategory__inner"><?
			
				if($show_subcat == 'both' && count($subcat) > 0 && !is_search())
				{
					?><div class="subcategories-list"><?
						?><div class="subcategories-list__container"><?
						
						foreach($subcat as $v)
						{
							?><a class="subcategories-list__item<?=$category->term_id == $v->term_id ? ' subcategories-list__item--active' : '' ?>" href="<?=get_term_link($v->term_id, 'product_cat') ?>"><?=$v->name ?></a><?
						}
						
						?></div><?
					?></div><?
			
					// Фильтр Подов
					
					//if(in_array($category->term_id, [269, 270]))
					//{
						$args = array( 
							'tax_query' => array(
								array( 
									'taxonomy' => 'product_cat',
									'field' => 'id',
									'terms' => $category->term_id
								),
							), 
							'posts_per_page' => 999,
							'post_type' => 'product',
						);	

						if(is_super_admin())
							$args['post_status'] = $big_data['admin_product_status'];

						$all_products_in_cat = new WP_Query($args);
						$ar_ids = [];
						
						if($all_products_in_cat->have_posts())
						{
							while($all_products_in_cat->have_posts())
							{
								global $post;
								$all_products_in_cat->the_post();
								$ar_ids[] = $post->ID;
							}
						}
						
						$attr = get_terms([
							'taxonomy' => 'pa_povod',
							'hide_empty' => true,
							'object_ids' => $ar_ids
						]);
						
						if($attr && count($attr) > 0)
						{
							?><div class="goods_filter"><?
								?><div class="goods_filter__title">Повод:</div><?
							
								if(isset($_GET['filter_povod']) && !empty($_GET['filter_povod']))
									$ar_povod = explode(",", $_GET['filter_povod']);
								else
									$ar_povod = [];
								
								foreach($attr as $v)
								{
									$_ar_povod = $ar_povod;
									$is_active = false;
									
									if(array_search($v->slug, $_ar_povod) !== FALSE)
									{
										$is_active = true;
										unset($_ar_povod[array_search($v->slug, $_ar_povod)]);
									}
									else
										$_ar_povod[] = $v->slug;
									
									if(count($_ar_povod) > 0)
									{
										?><a rel="nofollow" href="<?=get_term_link($category->term_id, 'product_cat') ?>?query_type_povod=or&filter_povod=<?=implode(",", $_ar_povod) ?>" class="goods_filter__item<?=$is_active ? " active" : "" ?>"><?=$v->name ?></a><?
									}
									else
									{
										?><a rel="nofollow" href="<?=get_term_link($category->term_id, 'product_cat') ?>" class="goods_filter__item<?=$is_active ? " active" : "" ?>"><?=$v->name ?></a><?
									}
								}
								
							?></div><?
						}
					//}
				}
				
				if(have_posts() && $category->term_id != 408)
				{
					?><div class="goods goods-tune">
						<div class="goods__inner">
							<div class="goods__body<?=$is_present || $is_hide_filter ? ' col5 goods__content-col5' : '' ?>"><?
							
								if(!$is_present && !$is_hide_filter)
								{
									?><div class="goods__sidebar"><?
										get_template_part('templates/sidebar'); 
									?></div><?
								}

								?><div class="goods__content"><?
								
									get_template_part('templates/sort');

									if(!$is_hide_filter && is_active_sidebar('shop-filter-result'))
										dynamic_sidebar('shop-filter-result');

									?><div class="goods__content-inner goods__content-item wr_goods"><?

										while(have_posts()) : the_post();
											get_template_part('templates/product');
										endwhile; 
									
									?></div><?
									
									wc_get_template('woocommerce/loop/pagination.php');	
								
								?></div>
							</div>
						</div>
					</div><?
				}

			?></div><?

			
			if(!$is_present && $is_hide_related != 'y')
			{
				$added_category_id = isset($_POST['added_category_id']) 
					? (int)$_POST['added_category_id'] 
					: 271;
				
				$tag_dop_id = 309;
				
				if($is_prazdnik)
					$tag_dop_id = 394;
				elseif($is_14_fevralya)
					$tag_dop_id = 436;
				elseif($is_den_materi)
					$tag_dop_id = 439;
				
				$ar_tax = array(
					array( 
						'taxonomy' => 'product_tag',
						'field' => 'id',
						'terms' => $tag_dop_id,
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
		
					?><div class="similar similar--tune similar__product sim-last similar--added">
						<div class="container">
							<div class="similar__title title">
								Так же вы можете добавить:
							</div><?
			
							$ar_cats = array_unique($ar_cats);
							
							if(count($ar_cats) > 0)
							{
								?><div class="subcategories-list subcategories-list--added"><?
									?><div class="subcategories-list__container"><?
									
										/*
										?><span class="subcategories-list__item<?=$added_category_id == 0 ? " subcategories-list__item--active" : "" ?>" data-href="0">Все подарки</span><?
										*/
										
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
								?><div class="similar__inner similar__inner--slider"><?
											
									foreach($ar_products[$added_category_id] as $product)
									{
										global $product;
										get_template_part('templates/product');
									}
								
								?></div><?
							?></div><?
						?></div><?
					?></div><?
				}

				wp_reset_postdata();
			}

			?>
		</div><?
	}
	
	
	foreach($cat_description as $k => $v)
		if(empty(trim($v)))
			unset($cat_description[$k]);
		
	if(count($cat_description) > 0)
	{
		?><div class="category_description__text category_description__text--bottom"><? 
			
			echo wpautop(trim(implode("\n", $cat_description)));
			
		?></div><?
	}

?></div><?

if($category->term_id == 248 || $category->parent == 248)
{
	?><style>.goods__sidebar-close + .widget_layered_nav{display:none}</style><?
}
	
get_footer();