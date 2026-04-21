<?
if(!defined('ABSPATH')) exit;

global $product, $big_data;

if(xs_get_option('xs_is_hide_messenger') == 'on')
	return;

?><div class="soc-order"><?
	if(!empty(xs_get_option('xs_social_link_vb')))
	{
		?><a class="soc-order__item soc-order__item--viber" href="viber://pa?chatURI=<?=xs_get_option('xs_social_link_vb')?>" target="_blank"></a><?
	}

	if(!empty(xs_get_option('xs_social_link_wa')))
	{
		?><a class="soc-order__item soc-order__item--whatsapp" href="https://wa.clck.bar/<?=xs_get_option('xs_social_link_wa') ?>?text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5.%20%D0%AF%20%D1%85%D0%BE%D1%87%D1%83%20%D0%BE%D1%84%D0%BE%D1%80%D0%BC%D0%B8%D1%82%D1%8C%20%D0%B7%D0%B0%D0%BA%D0%B0%D0%B7.%20%D0%9C%D0%BD%D0%B5%20%D0%BF%D0%BE%D0%BD%D1%80%D0%B0%D0%B2%D0%B8%D0%BB%D1%81%D1%8F%20%D1%82%D0%BE%D0%B2%D0%B0%D1%80 <? the_title() ?>." target="_blank"></a><?
	}

	if(!empty(xs_get_option('xs_social_link_teleg')))
	{
		?><a class="soc-order__item soc-order__item--telegram" href="https://t.me/<?=xs_get_option('xs_social_link_teleg')?>" target="_blank"></a><?
	}

	if(!empty(xs_get_option('xs_social_link_max')))
	{
		?><a class="soc-order__item soc-order__item--max" href="<?=xs_get_option('xs_social_link_max')?>" target="_blank"></a><?
	}

?></div><?