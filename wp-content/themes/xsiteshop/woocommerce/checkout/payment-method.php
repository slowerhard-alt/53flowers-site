<?

if(!defined('ABSPATH'))	exit;

?><li class="payment_method_<?=$gateway->id ?>"><?

	?><input id="payment_method_<?=$gateway->id ?>" type="radio" class="input-radio" name="payment_method" value="<? echo esc_attr($gateway->id) ?>" <? checked($gateway->chosen, true ) ?> data-order_button_text="<?=esc_attr($gateway->order_button_text ) ?>" /><?

	?><label for="payment_method_<?= $gateway->id ?>"><?
		
		echo $gateway->get_title(); ?> <? echo $gateway->get_icon();
		
	?></label><?
	
	if($gateway->has_fields() || $gateway->get_description())
	{
		?><div class="payment_box payment_method_<?=$gateway->id ?>" <? if(!$gateway->chosen){ ?>style="display:none;"<? } ?>><?
			$gateway->payment_fields();
		?></div><?
	}
	
?></li><?
