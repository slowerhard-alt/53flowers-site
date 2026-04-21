<?
if(!defined('ABSPATH')) exit;

?><div class="side_container xs_flex"><?

	?><div class="xs_sidebar"><?
		do_action('woocommerce_account_navigation');
	?></div><?
	
	?><div class="xs_content"><?
		do_action('woocommerce_account_content');
	?></div><?
	
?></div><?