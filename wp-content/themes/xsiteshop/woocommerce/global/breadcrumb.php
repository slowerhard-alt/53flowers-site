<?
global $product;

if($breadcrumb) 
{
    echo '<div class="breadcrumbs common-fade">
        <ul class="breadcrumbs__ul" itemscope itemtype="https://schema.org/BreadcrumbList">';

	if(mb_strpos($breadcrumb[1][1], "/news/category/", 0, "utf-8") !== false)
	{
		$news = get_post(3337);
		
		$breadcrumb[1] = array(
			$news->post_title,
			get_permalink($news->ID)
		);
	}
	
	if(is_product())
	{
		if($term_id = get_post_meta($product->get_id(), '_yoast_wpseo_primary_product_cat', true))
		{
			$new_breadcrumb[] = $breadcrumb[0];
			$new_breadcrumb[] = $breadcrumb[1];
			
			$link = get_term_parents_list($term_id, 'product_cat', ['format' => 'slug', 'link' => false]);
				
			if(substr($link, -1) == '/')
				$link = substr($link, 0, -1);
				
			$e = explode("/", $link);
			
			foreach($e as $v)
			{
				$term = get_term_by('slug', $v, 'product_cat');
				
				$new_breadcrumb[] = [$term->name, get_category_link($term->term_id)];
			}

			$new_breadcrumb[] = $breadcrumb[count($breadcrumb) - 1];
			
			$breadcrumb = $new_breadcrumb;
		}
	}
	
	$position = 1; // Инициализация счетчика позиции
		
    foreach ($breadcrumb as $key => $crumb) 
	{

        echo $before;

        if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) 
    	{
            echo '<li class="breadcrumbs__li" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a class="breadcrumbs__link" itemprop="item" href="' . esc_url( $crumb[1] ) . '"><span><span itemprop="name">' . esc_html( $crumb[0] ) . '</span><meta itemprop="position" content="'.$position++.'"></span></a>
                </li>';
    	}
        else
    	{
            echo '<li class="breadcrumbs__li">' . esc_html( $crumb[0] ). '</li>';
    	}
        
        echo $after;

        /*if ( sizeof( $breadcrumb ) !== $key + 1 )
            echo '<span class="delimiter"></span>';*/
    }
    echo '</ul></div>';
}