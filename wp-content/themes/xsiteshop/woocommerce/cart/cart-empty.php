<?
	if(!defined('ABSPATH')) exit;
	wc_print_notices();
?>

<div class="empty-box">
	<div class="look">
		<div class="image cart"></div>
		<div class="text"><?do_action('woocommerce_cart_is_empty');?></div>
	</div>
	<p class="return-to-shop"><?
		?><a class="button wc-backward btn" href="<?=get_term_link(233, 'product_cat') ?>"><? _e('Начать покупки') ?></a><?	
	?></p>
</div>



