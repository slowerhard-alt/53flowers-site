<?

if(!defined('ABSPATH')) exit;

if(!$messages) return;

foreach($messages as $message)
{
	if(mb_strpos($message, 'Вы отложили', 0, 'utf-8') !== false)
		continue;
	
	?><div class="container xs_message type-message"><?
		
		echo wp_kses_post(str_replace("/cart/", "/checkout/", $message));
		
		?><span class="notice-dismiss"></span><?
	?></div><?
}
