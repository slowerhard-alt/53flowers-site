<?

if(!defined('ABSPATH')) exit;

if(!$messages) return;

foreach($messages as $message)
{
	?><div class="container xs_message type-error"><?
		
		echo wp_kses_post(str_replace("/cart/", "/checkout/", $message));
		
		?><span class="notice-dismiss"></span><?
	?></div><?
}
