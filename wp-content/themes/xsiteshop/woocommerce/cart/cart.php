<?
if(!defined('ABSPATH')) exit;

global $woocommerce; 

?><meta http-equiv="Refresh" content="0; url=<?=$woocommerce->cart->get_checkout_url();?>"><?