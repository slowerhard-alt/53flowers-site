<?
if(!defined('ABSPATH')) exit;

global $wp_query;

if($wp_query->max_num_pages <= 1) return;

?><div class="goods__content-wrbtn xs_learn_more"><?
	?><a class="goods__content-btn" href="#" rel="nofollow">Показать еще</a><?
?></div><?

?><nav class="xs_pagination xs_pagination--tune"><?

	echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
		'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
		'format'       => '',
		'add_args'     => '',
		'current'      => max(1, get_query_var('paged')),
		'total'        => $wp_query->max_num_pages,
		'prev_next'	   => true,
		'prev_text'    => '&larr;',
		'next_text'    => '&rarr;',
		'type'         => 'list',
		'end_size'     => 3,
		'mid_size'     => 3
	) ) );

?></nav><?