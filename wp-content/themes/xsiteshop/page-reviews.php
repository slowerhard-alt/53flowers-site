<? 
get_header(); 

?><div class="page_content"><?

	?><div class="catalog_head"><?
		?><h1><? the_title() ?></h1><?
	?></div><?
	
	if(is_super_admin())
	{
		?><a target="_blank" href="/wp-admin/post.php?post=<?=$post->ID ?>&action=edit" class="xs_link_edit"></a><?
	}

	the_content();
	
?></div><?

?><div class="add_review_btn_container"><?
	?><div class="add_review_btn btn" onclick="jQuery('#add_review').toggleClass('active')">Добавить отзыв</div><?
?></div><?

?><form id="add_review" action="" class="xs_flex" method="post" enctype="multipart/form-data"><?
	?><div class="form"><?
		?><div class="xs_result"></div><?
		/* ?><div class="xs_flex inputter"><? */
			?><div class="xs_input"><?
				?><div class="label">Ваше имя *</div><?
				?><div class="input"><?
					?><input required value="" type="text" name="post_data[name]" placeholder="" /><?
				?></div><?
			?></div><?
			
			/*
			?><div class="xs_input"><?
				?><div class="label">Ваше фото</div><?
				?><div class="input"><?
					?><input type="file" accept="image/*,image/jpeg" name="xs_photo" placeholder="" /><?
				?></div><?
			?></div><?
			*/
			
		/* ?></div><? */
		?><div class="description"><?
			?><div class="label">Ваш отзыв *</div><?
			?><textarea required name="post_data[review]"></textarea><?
		?></div><?
		?><input type="hidden" name="post_data[send]" value="y" /><?
		
		?><div class="xs_flex xs_buttons"><?
			?><div class="input"><?
				?><input type="submit" name="" class="btn xs_yellow" value="Разместить отзыв" /><?
			?></div><? 
		?></div><? 
	?></div><?
	?><div class="image"><?
		?><img src="<? bloginfo('template_url') ?>/images/add_review.jpg" alt="" /><?
	?></div><?
?></form><?

?><script>if(window.location.hash.substr(1) == 'add_review_show') jQuery('#add_review').addClass('active')</script><?

global $wp_query;

$wp_query = new WP_Query(
	array(
		'post_type' 	  => 'review',
		'post_status'     => (is_super_admin() ? ['publish', 'draft'] : 'publish'),
		'orderby'		  => 'date',
		'posts_per_page'  => get_option('posts_per_page'),
		'paged' 		  => get_query_var('paged') ?: 1
	)
);


?>
<!-- Забрал из админки Ссылки на яндекс карты и 2гис -->
<!-- p class="has-text-align-left" style="font-size:18px"><a href="https://clck.ru/TJtFJ" target="_blank" rel="noreferrer noopener"><strong>Янд</strong></a><strong><a href="https://clck.ru/34vdQQ" target="_blank" rel="noreferrer noopener">екс.Карты</a></strong></p>

<p class="has-text-align-left" style="font-size:18px"><a href="https://clck.ru/TJtzP" target="_blank" rel="noreferrer noopener"><strong>2ГИС</strong></a></p> -->
<?
	
	/*?><iframe src="https://yandex.ru/sprav/widget/rating-badge/24404990314?type=rating" width="150" height="50" frameborder="0"></iframe><?*/


	/* Виджет отзывы START */
	?><div class="vidget-reviews">
		<div class="vidget-reviews__item">
			<div class="vidget-reviews__title">Наши отзывы на яндекс картах:</div>

			<div style="height:800px;overflow:hidden;position:relative;"><iframe style="width:100%;height:100%;border:1px solid #e6e6e6;border-radius:8px;box-sizing:border-box" src="https://yandex.ru/maps-reviews-widget/24404990314?comments"></iframe><a href="https://yandex.ru/maps/org/tsvety_ru/24404990314/" target="_blank" style="box-sizing:border-box;text-decoration:none;color:#b3b3b3;font-size:10px;font-family:YS Text,sans-serif;padding:0 20px;position:absolute;bottom:8px;width:100%;text-align:center;left:0;overflow:hidden;text-overflow:ellipsis;display:block;max-height:14px;white-space:nowrap;padding:0 16px;box-sizing:border-box">Цветы Pro на карте Великого Новгорода — Яндекс Карты</a></div>
		</div>

		<div class="vidget-reviews__item">
			<div class="vidget-reviews__title">Наши отзывы на 2ГИС:</div>
			<head>
			  <script type="text/javascript">
			    window.__size__='big';
			    window.__theme__='light';
			    window.__branchId__='70000001044219159';
			    window.__orgId__='';
			  </script>
			  <script crossorigin="anonymous" type="module" src="https://disk.2gis.com/widget-constructor/assets/iframe.js"></script>
			  <link rel="modulepreload" crossorigin="anonymous" href="https://disk.2gis.com/widget-constructor/assets/defaults.js">
			  <link rel="stylesheet" crossorigin="anonymous" href="https://disk.2gis.com/widget-constructor/assets/defaults.css">
			</head>
			<body>
			  <div id="iframe"></div>
			</body>
		</div>
	</div><?
	/* Виджет отзывы END */



if($wp_query->have_posts())
{
	?><div class="xs_review_page"><?
		?><div class="xs_review_page__title">Отзывы на нашем сайте:</div><?

		?><div class="sights xs_flex xs_wrap"><?
		
			while($wp_query->have_posts())
			{
				$wp_query->the_post();
				$review = $post;
				
				/*
				$url = wp_get_attachment_url(get_post_thumbnail_id($review->ID));
				
				if(!$url || empty($url))
					$url = get_bloginfo('template_url').'/images/pics/no-photo.jpg';
				else
					$url = xs_img_resize($url, 196, 196);
				*/
				
				?><div class="wr_inner_item xs_flex"><?
				
					/*
					?><div class="image" style="background-image:url(<?=$url ?>)"></div><?
					*/
					
					?><div class="review"><?
					
						?><div class="person xs_flex"><?
							?><div class="identety"><?
								?><strong><?=$review->post_title ?></strong><?
								?><span><?=mb_strtolower(xs_date($review->post_date), 'utf-8') ?></span><?
								
								if($review->post_status == 'draft')
								{
									?><a target="_blank" href="/wp-admin/post.php?post=<?=$review->ID ?>&action=edit" class="draft">Отзыв не опубликован и не виден пользователям</a><?
								}
								
							?></div><?
						?></div><?

						?><div class="inner"><?
						
							if(is_super_admin())
							{
								?><a target="_blank" href="/wp-admin/post.php?post=<?=$review->ID ?>&action=edit" class="xs_link_edit"></a><?
							}
					
							echo wpautop($review->post_content);
						?></div><?
						
						if(!empty($review->post_excerpt))
						{
							?><div class="answer"><?
								?><div class="arrow"></div><?
								?><div class="company_title">Администрация сайта</div><?
								?><div class="inner"><?
									echo wpautop($review->post_excerpt);
								?></div><?
							?></div><?
						}
						
					?></div><?
				?></div><?
			}

		?></div><?
	?></div><?

	?><div class="xs_pagination"><?

		echo paginate_links(array(
			'show_all'     => true,
			'end_size'     => 2,
			'mid_size'     => 5,
			'prev_next'    => true,
			'prev_text'    => __('←'),
			'next_text'    => __('→'),
			'add_args'     => false,
			'add_fragment' => '',
			'screen_reader_text' => "",
			'type'         => 'list'
		));

	?></div><?
}
else
{
	?><p>Отзывов пока нет.</p><?
}

get_footer();

/*

<div class="frame_reviews desktop">
	<iframe class="_3Xz9Z" title="Embedded Content" name="htmlComp-iframe" width="100%" height="100%" data-src="" src="https://www-53flowers-com.filesusr.com/html/db888d_cf5e570eb2ad30bd28067a3507702a3c.html"></iframe>
</div>

<div class="frame_reviews mobile">
	<? the_content() ?>
	<div class="wrap-iframe">
		<iframe src="https://yandex.ru/sprav/widget/rating-badge/112831692676" width="150" height="50" frameborder="0"></iframe>
	</div>
</div>


<!-- <noscript>
	<div>
		<img src="https://mc.yandex.ru/watch/57020224" style="position:absolute;left:-9999px" alt=""/>
	</div>
</noscript>

<div class="mini-badge"><a
      href="https://yandex.ru/maps/org/tsvety_ru/4817249531?utm_source=maps-reviews-widget&amp;utm_medium=reviews&amp;utm_content=org-name"
      target="_blank" class="mini-badge__org-name">Цветы.ру</a>
   <div class="mini-badge__rating-info">
      <p class="mini-badge__stars-count">5.0</p>
      <div>
         <div class="mini-badge__stars">
            <ul class="stars-list">
               <li class="stars-list__star"></li>
               <li class="stars-list__star"></li>
               <li class="stars-list__star"></li>
               <li class="stars-list__star"></li>
               <li class="stars-list__star"></li>
            </ul>
         </div>
         	<a class="mini-badge__rating" target="_blank"
            href="https://yandex.ru/maps/org/tsvety_ru/4817249531/reviews?utm_source=maps-reviews-widget&amp;utm_medium=reviews&amp;utm_content=rating">107
            отзывов • 137 оценок</a>
      </div>
   </div>
   <div class="mini-badge__logo">
   		<a href="https://yandex.ru/maps?utm_source=maps-reviews-widget&amp;utm_medium=reviews&amp;utm_content=logo" target="_blank" class="logo _link"></a>
   </div>
</div>

<div class="badge__form">
   <p class="badge__form-text">Поставьте нам оценку</p><a
      href="https://yandex.ru/maps/org/tsvety_ru/4817249531/reviews?utm_source=maps-reviews-widget&amp;utm_medium=reviews&amp;utm_content=add_review&amp;add-review"
      class="badge__link-to-map" target="_blank">Оставить отзыв</a>
</div> -->

<?/* if(xs_get_option('xs_show_reviews') == true)
{	
	?><div class="xs_flex xs_top title_panel"><?
		?><h1><? the_title() ?></h1><?
		?><div class="btn xs_blue xs_opacity" onclick="jQuery('#add_review').toggleClass('active')">Добавить отзыв</div><?
	?></div><?
	
	?><form id="add_review" action="" class="xs_flex" method="post" enctype="multipart/form-data"><?
		?><div class="form"><?
			?><div class="xs_result"></div><?
			?><div class="xs_flex inputter"><?
				?><div class="xs_input"><?
					?><div class="label">Ваше имя *</div><?
					?><div class="input"><?
						?><input required value="" type="text" name="post_data[name]" placeholder="" /><?
					?></div><?
				?></div><?
				?><div class="xs_input"><?
					?><div class="label">Ваше фото</div><?
					?><div class="input"><?
						?><input type="file" accept="image/*,image/jpeg" name="xs_photo" placeholder="" /><?
					?></div><?
				?></div><?
			?></div><?
			?><div class="description"><?
				?><div class="label">Ваш отзыв *</div><?
				?><textarea required name="post_data[review]"></textarea><?
			?></div><?
			?><input type="hidden" name="post_data[send]" value="y" /><?
			
			?><div class="xs_flex xs_buttons"><?
				?><div class="input"><?
					?><input type="submit" name="" class="btn xs_yellow" value="Разместить отзыв" /><?
				?></div><? 
			?></div><? 
		?></div><?
		?><div class="image"><?
			?><img src="<? bloginfo('template_url') ?>/images/pics/add_review.jpg" alt="" /><?
		?></div><?
	?></form><?
	
	?><script>if(window.location.hash.substr(1) == 'add_review_show') jQuery('#add_review').addClass('active')</script><?
	
	global $wp_query;
	
	$wp_query = new WP_Query(
		array(
			'post_type' 	  => 'review',
			'post_status'     => (is_super_admin() ? ['publish', 'draft'] : 'publish'),
			'orderby'		  => 'date',
			'posts_per_page'  => get_option('posts_per_page'),
			'paged' 		  => get_query_var('paged') ?: 1
		)
	);
	
	
	if($wp_query->have_posts())
	{
		?><div class="xs_review_page"><?
			?><div class="sights xs_flex xs_wrap"><?
			
				while($wp_query->have_posts())
				{
					$wp_query->the_post();
					$review = $post;
					
					$url = wp_get_attachment_url(get_post_thumbnail_id($review->ID));
					
					if(!$url || empty($url))
						$url = get_bloginfo('template_url').'/images/pics/no-photo.jpg';
					else
						$url = xs_img_resize($url, 196, 196);
					
					?><div class="wr_inner_item xs_flex"><?
						?><div class="image" style="background-image:url(<?=$url ?>)"></div><?
						?><div class="review"><?
						
							?><div class="person xs_flex"><?
								?><div class="identety"><?
									?><strong><?=$review->post_title ?></strong><?
									?><span><?=mb_strtolower(xs_date($review->post_date), 'utf-8') ?></span><?
									
									if($review->post_status == 'draft')
									{
										?><a target="_blank" href="/wp-admin/post.php?post=<?=$review->ID ?>&action=edit" class="draft">Отзыв не опубликован и не виден пользователям</a><?
									}
									
								?></div><?
							?></div><?

							?><div class="inner"><?
							
								if(is_super_admin())
								{
									?><a target="_blank" href="/wp-admin/post.php?post=<?=$review->ID ?>&action=edit" class="xs_link_edit"></a><?
								}
						
								echo wpautop($review->post_content);
							?></div><?
							
							if(!empty($review->post_excerpt))
							{
								?><div class="answer"><?
									?><div class="arrow"></div><?
									?><div class="company_title">Администрация сайта</div><?
									?><div class="inner"><?
										echo wpautop($review->post_excerpt);
									?></div><?
								?></div><?
							}
							
						?></div><?
					?></div><?
				}

			?></div><?
		?></div><?

		?><div class="xs_pagination"><?

			echo paginate_links(array(
				'show_all'     => true,
				'end_size'     => 2,
				'mid_size'     => 5,
				'prev_next'    => true,
				'prev_text'    => __('←'),
				'next_text'    => __('→'),
				'add_args'     => false,
				'add_fragment' => '',
				'screen_reader_text' => "",
				'type'         => 'list'
			));

		?></div><?
	}
	else
	{
		?><p>Отзывов пока нет.</p><?
	}
}
else
{
	while(have_posts()) : the_post();

		?><h1><? the_title() ?></h1><?	
	
		the_content();
		
	endwhile; 	
}*/