<?

add_action( 'wp_enqueue_scripts', 'xs_add_scripts', 11);

function xs_add_scripts() 
{
	$v = 196;

	wp_enqueue_style("xs_style_css", get_bloginfo('template_directory') . "/style.css?p=13", false, $v, "all");
	wp_enqueue_style("xs_responsive_css", get_bloginfo('template_directory') . "/css/responsive.css?p=33", false, $v, "all");

	wp_enqueue_script("xs_slick_js", get_bloginfo('template_directory') . "/js/slick.min.js", 'jquery', $v, true);
	wp_enqueue_script("xs_fancybox_js", get_bloginfo('template_directory') . "/js/jquery.fancybox.js", 'jquery', $v, true);
	wp_enqueue_script("xs_inputmask_js", get_bloginfo('template_directory') . "/js/jquery.inputmask.js", 'jquery', $v, true);

	if(is_front_page())
	{
		wp_enqueue_script("jquery-ui-core-js", "/wp-includes/js/jquery/ui/core.min.js", 'jquery', $v, true);
		wp_enqueue_script("jquery-ui-mouse-js", "/wp-includes/js/jquery/ui/mouse.min.js", 'jquery', $v, true);
		wp_enqueue_script("jquery-ui-slider-js", "/wp-includes/js/jquery/ui/slider.min.js", 'jquery', $v, true);
		wp_enqueue_script("wc-jquery-ui-touchpunch-js", "/wp-content/plugins/woocommerce/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch.min.js", 'jquery', $v, true);
		wp_enqueue_script("accounting-js", "/wp-content/plugins/woocommerce/assets/js/accounting/accounting.min.js", 'jquery', $v, true);

		wp_enqueue_script("wc-price-slider-js", "/wp-content/plugins/woocommerce/assets/js/frontend/price-slider.min.js", 'jquery', $v, true);
	}

	if(is_product())
		wp_enqueue_script("selectator-js", get_bloginfo('template_directory') . "/js/selectator.jquery.js", 'jquery', $v, true);

	if(is_checkout())
	{
		wp_enqueue_style("xs_datepicker_css", get_bloginfo('template_directory') . "/css/datepicker.min.css", false, $v, "all");

		wp_enqueue_script("datepicker-js", get_bloginfo('template_directory') . "/js/datepicker.js", 'jquery', $v, true);
		wp_enqueue_script("checkout-js", get_bloginfo('template_directory') . "/js/checkout.js", 'jquery', $v, true);
	}

	wp_enqueue_script("xs_cookie_js", get_bloginfo('template_directory') . "/js/jquery.cookie.js", 'jquery', $v, true);	

	wp_enqueue_script("xs_template_js", get_bloginfo('template_directory') . "/js/template.js?p=6", 'jquery', $v, true);

	wp_deregister_style('wc-block-vendors-style');
    wp_dequeue_style('wc-block-vendors-style');
}

add_action( 'after_setup_theme', 'woocommerce_support' );

function woocommerce_support() 
{
    add_theme_support( 'woocommerce' );
}

function set_color($hex, $steps) 
{
    $steps = max(-255, min(255, $steps));

    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}

function set_color_svg($path, $color)
{
	$old_svg = file_get_contents($path);

	$svg = preg_replace("/fill=(\'|\")(.*?)(\'|\")/", "fill=\"".$color."\"", $old_svg); 

	file_put_contents($path, $svg); 
}

define('WOOCOMMERCE_USE_CSS', false);
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

function mytheme_add_admin()
{
    global $themename, $shortname, $options, $big_data;

    if ($_GET['page'] == "xs_setting" && $_REQUEST['action'] == 'saved') 
	{
		foreach ($options as $tab) 
			if ($_POST["tab"] == $tab['code']) 
			{
				$xs = $tab;
				$tab_name = $tab['code'];
			}

		if (!isset($xs))
		{
			$xs = $options[0];
			$tab_name = $options[0]['code'];
		}

		foreach ($xs['group'] as $group)
		{
			foreach ($group['options'] as $option)
				$opt[] = $option;
		}

		foreach ($opt as $value) 
		{
			if (isset ($_REQUEST[$value['id']]))
				$xs_options[$value['id']] = $_REQUEST[$value['id']];
		}

		if($tab_name == 'main')
		{
			if(isset($_REQUEST['xs_get_register']) && !empty($_REQUEST['xs_get_register']))
			{
				update_option('users_can_register', 1);
				update_option('woocommerce_enable_checkout_login_reminder', 'yes');
				update_option('woocommerce_enable_signup_and_login_from_checkout', 'yes');
				update_option('woocommerce_enable_myaccount_registration', 'yes');
			}
			else
			{
				update_option('users_can_register', 0);
				update_option('woocommerce_enable_checkout_login_reminder', 'no');
				update_option('woocommerce_enable_signup_and_login_from_checkout', 'no');
				update_option('woocommerce_enable_myaccount_registration', 'no');
			}

			$xs_options['footer_cats'] = xs_get_option('footer_cats');
		}

		if($tab_name == 'shop')
		{
			if(isset($_REQUEST['xs_shop_review']) && !empty($_REQUEST['xs_shop_review']))
				update_option('woocommerce_enable_reviews', 'yes');
			else
				update_option('woocommerce_enable_reviews', 'no');

			if(isset($_REQUEST['xs_shop_is_coupon']) && !empty($_REQUEST['xs_shop_is_coupon']))
				update_option('woocommerce_enable_coupons', 'yes');
			else
				update_option('woocommerce_enable_coupons', 'no');
		}

		if($tab_name == 'shop')
		{	
			if(isset($_REQUEST['xs_shop_cat_count_product']) && !empty($_REQUEST['xs_shop_cat_count_product']))
			{
				update_option('posts_per_page', $_REQUEST['xs_shop_cat_count_product']);
			}
		}		

		update_option('xs_options_'.$tab_name, $xs_options);

		xs_clear_cache();

		if($tab_name == 'store')
		{	
			foreach($options as $v)
			{
				if($ar = get_option('xs_options_'.$v['code']))
					$big_data['options'] = array_merge($big_data['options'], $ar);
			}

			update_price_component();
		}

		wp_cache_flush();
		header("Location: admin.php?page=xs_setting&saved=true&tab=" . $_POST["tab"]);
		die;
    }

    add_menu_page($themename, $themename, 'manage_options', 'xs_setting', 'mytheme_admin', '', 57);

    foreach ($options as $tab)
        add_submenu_page('xs_setting', $tab['name'], $tab['name'], 'manage_options', 'admin.php?page=xs_setting&tab=' . $tab['code'], '');

    remove_submenu_page('xs_setting', 'xs_setting');

	add_filter('woocommerce_marketing_menu_items', 'woocommerce_marketing_menu_items');
	function woocommerce_marketing_menu_items($marketing_pages)
	{
		return array();
	}
}

add_action('admin_menu', 'mytheme_add_admin');

function mytheme_add_init()
{
    wp_enqueue_style("functions", get_bloginfo('template_directory') . "/css/admin.css", false, "7.4", "all");
    wp_enqueue_style("fancybox", get_bloginfo('template_directory') . "/css/pending.css", false, "1.4", "all");
    wp_enqueue_style("datepicker_css", get_bloginfo('template_directory') . "/css/datepicker.min.css", false, $version, "all");

	wp_enqueue_script("functions", get_bloginfo('template_directory') . "/js/admin.js", false, "7.2");
	wp_localize_script("functions", "xsAdmin", ["nonce" => wp_create_nonce("xs_admin_action")]);

	wp_add_inline_script('jquery-core', 'jQuery(function(){' .
		'if(typeof _==="undefined")return;' .
		'var isLodash=_.VERSION&&/^4\\./.test(_.VERSION);' .
		'if(isLodash){' .
		'  var lod=_.noConflict();' .  // restore previous _ (underscore if was set before)
		'  if(typeof _!=="undefined"&&_.VERSION&&!/^4\\./.test(_.VERSION)){' .
		'    window.lodash=lod;' .
		'  } else {' .
		'    window._=lod;' .
		'    var wCtx=function(fn){return function(c,f,ctx){return ctx!==undefined?fn(c,function(){return f.apply(ctx,arguments)}):fn(c,f);};};' .
		'    _.each=_.forEach=wCtx(_.each);' .
		'    _.map=_.collect=wCtx(_.map);' .
		'    _.filter=_.select=wCtx(_.filter);' .
		'    _.find=_.detect=wCtx(_.find);' .
		'    _.every=_.all=wCtx(_.every);' .
		'    _.some=_.any=wCtx(_.some);' .
		'    if(_.prototype){' .
		'      var wCP=function(orig){return function(f,ctx){if(ctx!==undefined){var of2=f;f=function(){return of2.apply(ctx,arguments);};}return orig.call(this,f);};};' .
		'      if(_.prototype.forEach)_.prototype.each=_.prototype.forEach=wCP(_.prototype.forEach);' .
		'      if(_.prototype.map)_.prototype.map=_.prototype.collect=wCP(_.prototype.map);' .
		'      if(_.prototype.filter)_.prototype.filter=_.prototype.select=wCP(_.prototype.filter);' .
		'      if(_.prototype.find)_.prototype.find=_.prototype.detect=wCP(_.prototype.find);' .
		'      if(_.prototype.every)_.prototype.every=_.prototype.all=wCP(_.prototype.every);' .
		'      if(_.prototype.some)_.prototype.some=_.prototype.any=wCP(_.prototype.some);' .
		'    }' .
		'  }' .
		'}' .
		'if(typeof _!=="undefined"&&_.mixin){' .
		'  var m={};' .
		'  if(!_.any&&_.some)m.any=_.some;' .
		'  if(!_.contains&&_.includes)m.contains=_.includes;' .
		'  if(!_.all&&_.every)m.all=_.every;' .
		'  if(!_.detect&&_.find)m.detect=_.find;' .
		'  if(!_.select&&_.filter)m.select=_.filter;' .
		'  if(!_.pluck&&_.map)m.pluck=function(o,k){return _.map(o,_.property(k));};' .
		'  if(Object.keys(m).length)_.mixin(m);' .
		'}' .
	'});', 'after');
	wp_enqueue_script("datepicker", get_bloginfo('template_directory') . "/js/datepicker.js", false, $version, true);
	wp_enqueue_script("xs_inputmask_js", get_bloginfo('template_directory') . "/js/jquery.inputmask.js", 'jquery', $version, true);
    wp_enqueue_script("fancybox", get_bloginfo('template_directory') . "/js/jquery.fancybox.js", false, "1.1");
}

if(is_admin())
	add_action('admin_init', 'mytheme_add_init');

function dco_remove_default_image_sizes( $sizes) {
	return array_diff( $sizes, array(
		'shop_catalog',
		'medium',
		'medium_large',
		'large'
	) );
}

add_filter('intermediate_image_sizes', 'dco_remove_default_image_sizes');

function xs_product_category_base_same_shop_base($flash = false)
{
    $terms = get_terms(array(
        'taxonomy' => 'product_cat',
        'post_type' => 'product',
        'hide_empty' => false,
    ));

    if($terms && !is_wp_error($terms)) 
	{
        $siteurl = esc_url(home_url('/'));

        foreach ($terms as $term) 
		{
            $term_slug = $term->slug;
            $baseterm = str_replace($siteurl, '', get_term_link($term->term_id, 'product_cat'));
            add_rewrite_rule($baseterm . '?$','index.php?product_cat=' . $term_slug,'top');
            add_rewrite_rule($baseterm . 'page/([0-9]{1,})/?$', 'index.php?product_cat=' . $term_slug . '&paged=$matches[1]','top');
            add_rewrite_rule($baseterm . '(?:feed/)?(feed|rdf|rss|rss2|atom)/?$', 'index.php?product_cat=' . $term_slug . '&feed=$matches[1]','top');
        }
    }
    if($flash == true)
        flush_rewrite_rules(false);
}
add_filter('init', 'xs_product_category_base_same_shop_base');

add_action( 'create_term', 'devvn_product_cat_same_shop_edit_success', 10, 2 );
function devvn_product_cat_same_shop_edit_success( $term_id, $taxonomy ) 
{
    xs_product_category_base_same_shop_base(true);
}  

if(is_admin())
{

	remove_action( 'admin_init', '_maybe_update_core' );
	remove_action( 'admin_init', '_maybe_update_plugins' );
	remove_action( 'admin_init', '_maybe_update_themes' );

	remove_action( 'load-plugins.php', 'wp_update_plugins' );
	remove_action( 'load-themes.php', 'wp_update_themes' );

    add_filter( 'pre_site_transient_browser_'. md5( $_SERVER['HTTP_USER_AGENT'] ), '__return_empty_array' );
}
