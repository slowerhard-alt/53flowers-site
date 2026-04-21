<?

if(!defined('ABSPATH'))	exit;

if(is_super_admin())
{
	?><a target="_blank" href="/wp-admin/admin.php?page=wc-settings&tab=shipping" class="xs_link_edit"></a><?
}

?><table class="shop_table woocommerce-checkout-review-order-table"><?
	?><tbody><?

		/*
		?><tr class="cart-subtotal"><?
			?><th><? _e('Subtotal', 'woocommerce') ?></th><?
			?><td><? wc_cart_totals_subtotal_html() ?></td><?
		?></tr><?
		*/
		
		foreach(WC()->cart->get_coupons() as $code => $coupon)
		{
			?><tr class="cart-discount coupon-<?=esc_attr($code) ?>"><?
				?><th><? wc_cart_totals_coupon_label($coupon) ?></th><?
				?><td><? wc_cart_totals_coupon_html($coupon) ?></td><?
			?></tr><?
		}

		if(WC()->cart->needs_shipping() && WC()->cart->show_shipping())
		{
			do_action('woocommerce_review_order_before_shipping');
			wc_cart_totals_shipping_html();
			do_action('woocommerce_review_order_after_shipping');
		}

		foreach(WC()->cart->get_fees() as $fee)
		{
			?><tr class="fee"><?
				?><th><?=esc_html($fee->name) ?></th><?
				?><td><? wc_cart_totals_fee_html($fee) ?></td><?
			?></tr><?
		}

		if(WC()->cart->tax_display_cart === 'excl')
		{
			if(get_option('woocommerce_tax_total_display') === 'itemized')
			{
				foreach( WC()->cart->get_tax_totals() as $code => $tax)
				{
					?><tr class="tax-rate tax-rate-<?=sanitize_title($code) ?>"><?
						?><th><?=esc_html($tax->label) ?></th><?
						?><td><?=wp_kses_post($tax->formatted_amount) ?></td><?
					?></tr><?
				}
			}
			else
			{
				?><tr class="tax-total"><?
					?><th><?=esc_html(WC()->countries->tax_or_vat()) ?></th><?
					?><td><?=wc_price( WC()->cart->get_taxes_total()) ?></td><?
				?></tr><?
			}
		}

		do_action('woocommerce_review_order_before_order_total');

		?><tr class="order-total"><?
			?><th><? _e( 'Total', 'woocommerce') ?></th><?
			?><td><? wc_cart_totals_order_total_html() ?></td><?
		?></tr><?

		do_action('woocommerce_review_order_after_order_total');

	?></tbody><?
?></table><?