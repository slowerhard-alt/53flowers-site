<?php

/**
 * Trait for simple products.
 *
 * @link       https://icopydoc.ru
 * @since      4.1.0
 * @version    4.1.0 (22-03-2026)
 *
 * @package    XFGMC
 * @subpackage XFGMC/includes/feeds/traits/simple
 */

/**
 * The trait adds `get_ads_redirect` method.
 * 
 * This method allows you to return the `ads_redirect` tag.
 *
 * @since      4.1.0
 * @package    XFGMC
 * @subpackage XFGMC/includes/feeds/traits/simple
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 * @depends    classes:     XFGMC_Get_Paired_Tag
 *             methods:     get_product
 *                          get_feed_id
 *                          get_simple_product_post_meta
 *                          get_simple_tag
 *             functions:   common_option_get
 */
trait XFGMC_T_Simple_Get_Ads_Redirect {

	/**
	 * Get `ads_redirect` tag.
	 * 
	 * @see https://support.google.com/merchants/answer/6324450
	 * 
	 * @param string $tag_name
	 * @param string $result_xml
	 * 
	 * @return string Example: `<g:ads_redirect>https://tracking.example.com?product=ballpoint-pens</g:ads_redirect>`.
	 */
	public function get_ads_redirect( $tag_name = 'g:ads_redirect', $result_xml = '' ) {

		$ads_redirect = common_option_get(
			'xfgmc_ads_redirect',
			'disabled',
			$this->get_feed_id(),
			'xfgmc'
		);
		if ( $ads_redirect === 'enabled' ) {
			$tag_value = $this->get_simple_product_post_meta( 'ads_redirect' );
			if ( empty( $tag_value ) ) {
				$tag_value = common_option_get(
					'xfgmc_ads_redirect_default_value',
					'',
					$this->get_feed_id(),
					'xfgmc'
				);
			}
			$result_xml = $this->get_simple_tag( $tag_name, $tag_value );
		}
		return $result_xml;

	}

}