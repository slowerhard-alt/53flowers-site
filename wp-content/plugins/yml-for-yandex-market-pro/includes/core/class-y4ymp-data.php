<?php

/**
 * Set and Get the Plugin Data.
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 * @version    6.1.0 (16-10-2026)
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/includes/core
 */

/**
 * Set and Get the Plugin Data.
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/includes/core
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
class Y4YMP_Data {

	/**
	 * Set and Get the Plugin Data.
	 */
	public function __construct() {

	}

	/**
	 * Get the plugin data array.
	 * 
	 * @return array
	 */
	public function get_data_arr() {

		$data_arr = [
			[
				'opt_name' => 'y4ymp_add_name_beginning_all_1',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add to all product names (at the beginning)', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'Add the following attribute to the beginning of all product names',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => true,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market-pro' ) ]
					],
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_add_name_beginning_all_2',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add to all product names (at the beginning)', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'Add the following attribute to the beginning of all product names',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => true,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_add_name_beginning_all_3',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add to all product names (at the beginning)', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'Add the following attribute to the beginning of all product names',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => true,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_add_name_end_all_1',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add to all product names (at the end)', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'Add the following attribute to the end of all product names',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => true,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market-pro' ) ]
					],
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_add_name_end_all_2',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add to all product names (at the end)', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'Add the following attribute to the end of all product names',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => true,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_add_name_end_all_3',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add to all product names (at the end)', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'Add the following attribute to the end of all product names',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => true,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_simple_name_var0',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add to simple product names (at the end)', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'Add the following attribute to the end of simple product names',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => true,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market-pro' ) ]
					],
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_simple_name_var1',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add to simple product names (at the end)', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'Add the following attribute to the end of simple product names',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => true,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_simple_name_var2',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add to simple product names (at the end)', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'Add the following attribute to the end of simple product names',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => true,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_disabled_ymarket',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Categories for Yandex Market', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'Add the following attribute to the beginning of all product names',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market-pro' ) ]
					],
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_tags_as_cat',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Use tags as categories', 'yml-for-yandex-market-pro' ),
					'desc' => '',
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'on_cat', 'text' => __( 'Yes. Priority categories', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'on', 'text' => __( 'Yes. Priority tags', 'yml-for-yandex-market-pro' ) ],
					]
				]
			],
			[
				'opt_name' => 'y4ymp_use_utm',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add UTM', 'yml-for-yandex-market-pro' ),
					'desc' => __( 'Add UTM tags to products URL', 'yml-for-yandex-market-pro' ),
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market-pro' ) ]
					],
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_utm_source',
				'def_val' => 'market.yandex.ru',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Referral source', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '%s utm_source',
						__( 'UTM tag', 'yml-for-yandex-market-pro' )
					),
					'placeholder' => sprintf( '%s utm_source',
						__( 'UTM tag', 'yml-for-yandex-market-pro' )
					)
				]
			],
			[
				'opt_name' => 'y4ymp_utm_medium',
				'def_val' => 'cpc',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Traffic type', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '%s utm_medium. %s: <code>cpc</code>, <code>social</code>, <code>organic</code>, <code>content</code>, <code>paidsearch</code>.<br/>%s <code>{catid}</code> %s <code>{productid}</code>',
						__( 'UTM tag', 'yml-for-yandex-market-pro' ),
						__( 'For example', 'yml-for-yandex-market-pro' ),
						__( 'You can add category ID by adding', 'yml-for-yandex-market-pro' ),
						__( 'and product ID', 'yml-for-yandex-market-pro' )
					),
					'placeholder' => ''
				]
			],
			[
				'opt_name' => 'y4ymp_utm_campaign',
				'def_val' => '',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'The name of the advertising campaign', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '%s utm_campaign. %s <code>{catid}</code> %s <code>{productid}</code>',
						__( 'UTM tag', 'yml-for-yandex-market-pro' ),
						__( 'You can add category ID by adding', 'yml-for-yandex-market-pro' ),
						__( 'and product ID', 'yml-for-yandex-market-pro' )
					),
					'placeholder' => '',
				]
			],
			[
				'opt_name' => 'y4ymp_utm_content',
				'def_val' => 'catid_prodid_slug',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Content variant', 'yml-for-yandex-market-pro' ),
					'desc' => __( 'UTM tag', 'yml-for-yandex-market-pro' ) . ': utm_content',
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'catid', 'text' => __( 'Category ID', 'yml-for-yandex-market-pro' ) ],
						[
							'value' => 'productid',
							'text' => __( 'Product ID', 'yml-for-yandex-market-pro' ),
						],
						[ 'value' => 'product_slug', 'text' => __( 'Product slug', 'yml-for-yandex-market-pro' ) ],
						[
							'value' => 'catid_prodid',
							'text' => sprintf( 'cat{%s}--prod{%s}',
								__( 'Category ID', 'yml-for-yandex-market-pro' ),
								__( 'Product ID', 'yml-for-yandex-market-pro' )
							)
						],
						[
							'value' => 'catid_prodid_slug',
							'text' => sprintf( 'cat{%s}--prod{%s}-{%s}',
								__( 'Category ID', 'yml-for-yandex-market-pro' ),
								__( 'Product ID', 'yml-for-yandex-market-pro' ),
								__( 'Product slug', 'yml-for-yandex-market-pro' )
							)
						]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_utm_term',
				'def_val' => '',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Key term', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf(
						'%1$s utm_term.<br/>%2$s <code>{catid}</code>; %3$s - <code>{productorvarid}</code>; %4$s - <code>{productslug}</code>; %5$s - <code>{productid}</code>',
						__( 'UTM tag', 'yml-for-yandex-market-pro' ),
						__( 'You can add category ID by adding', 'yml-for-yandex-market-pro' ),
						__( 'product ID or variation ID', 'yml-for-yandex-market-pro' ),
						__( 'product slug', 'yml-for-yandex-market-pro' ),
						__( 'and product ID', 'yml-for-yandex-market-pro' )
					),
					'placeholder' => ''
				]
			],
			[
				'opt_name' => 'y4ymp_roistat',
				'def_val' => '',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Roistat', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '%s rs. %s <code>{catid}</code> %s <code>{productorvarid}</code> %s <code>{productid}</code>',
						__( 'Roistat tag', 'yml-for-yandex-market-pro' ),
						__( 'You can add category ID by adding', 'yml-for-yandex-market-pro' ),
						__( 'product ID or variation ID', 'yml-for-yandex-market-pro' ),
						__( 'and product ID', 'yml-for-yandex-market-pro' )
					),
					'placeholder' => ''
				]
			],
			[
				'opt_name' => 'y4ymp_use_del_vc',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Remove VC tags', 'yml-for-yandex-market-pro' ),
					'desc' => __( 'Remove the Visual Composer tags from the description', 'yml-for-yandex-market-pro' ),
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'on', 'text' => __( 'Enabled', 'yml-for-yandex-market-pro' ) ]
					],
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_price_post_meta',
				'def_val' => '',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Price from post_meta', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '%s %s',
						__( 'Name post_meta', 'yml-for-yandex-market-pro' ),
						__(
							'If you enter the name of the meta field here, the value of the price will be taken from it',
							'yml-for-yandex-market-pro'
						)
					),
					'placeholder' => '',
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_multiply_price_value',
				'def_val' => '1',
				'mark' => 'public',
				'required' => false,
				'type' => 'number',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Multiply the price', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '<strong>%s!</strong> %s. %s.<br/>%s)',
						__( 'Warning', 'yml-for-yandex-market-pro' ),
						__(
							'This option is considered obsolete and will be removed in future versions of the plugin',
							'yml-for-yandex-market-pro'
						),
						__(
							'Instead, it is recommended to use the option below', 'yml-for-yandex-market-pro'
						),
						__(
							'The price of the product will be multiplied by the value from this field. Specify 1 so that the price does not change',
							'yml-for-yandex-market-pro'
						) ),
					'placeholder' => '1',
					'step' => '0.01',
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_price_percentage',
				'def_val' => '0',
				'mark' => 'public',
				'required' => false,
				'type' => 'number',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => sprintf( '%s (%s %% %s)',
						__( 'Change the product price', 'yml-for-yandex-market-pro' ),
						__( 'add or subtract', 'yml-for-yandex-market-pro' ),
						__( 'of the original cost of the product', 'yml-for-yandex-market-pro' )
					),
					'desc' => __( 'Negative values can be used', 'yml-for-yandex-market-pro' ),
					'placeholder' => '0',
					'step' => '0.5'
				]
			],
			[
				'opt_name' => 'y4ymp_add_to_price_value',
				'def_val' => '0',
				'mark' => 'public',
				'required' => false,
				'type' => 'number',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add to product price this value', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '%s. %s',
						__(
							'This value will be added to the value of the product. Specify 0 so that the price does not change',
							'yml-for-yandex-market-pro'
						),
						__( 'Negative values can be used', 'yml-for-yandex-market-pro' )
					),
					'placeholder' => '0',
					'step' => '0.01',
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_round_price_value',
				'def_val' => 'hundredths',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Round the price to', 'yml-for-yandex-market-pro' ),
					'desc' => '',
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'hundredths', 'text' => __( 'hundredths', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'integers', 'text' => __( 'integers', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'tens', 'text' => __( 'tens', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'hundreds', 'text' => __( 'hundreds', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'thousands', 'text' => __( 'thousands', 'yml-for-yandex-market-pro' ) ],
					]
				]
			],
			[
				'opt_name' => 'y4ymp_skip_vendor_names',
				'def_val' => '',
				'mark' => 'public',
				'required' => false,
				'type' => 'textarea',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Skip vendor', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '%s %s "%s" <a
				href="https://icopydoc.ru/kak-filtrovat-tovary-po-nazvaniyu-brenda-instruktsiya/?utm_source=yml-for-yandex-market&utm_medium=documentation&utm_campaign=yml-for-yandex-market-pro&utm_content=settings-page&utm_term=skip-vendor"
				target="_blank">%s</a>',
						__( 'Products from these brands are will not be add to the feed', 'yml-for-yandex-market-pro' ),
						__( 'The source of brands is configured on the tab', 'yml-for-yandex-market-pro' ),
						__( 'Attribute settings', 'yml-for-yandex-market-pro' ),
						__( 'Read more', 'yml-for-yandex-market-pro' )
					),
					'placeholder' => __( 'Enter brand names separated by semicolons', 'yml-for-yandex-market-pro' ),
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_only_vendor_names',
				'def_val' => '',
				'mark' => 'public',
				'required' => false,
				'type' => 'textarea',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Only these brands', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '%s %s "%s" <a
				href="https://icopydoc.ru/kak-filtrovat-tovary-po-nazvaniyu-brenda-instruktsiya/?utm_source=yml-for-yandex-market&utm_medium=documentation&utm_campaign=yml-for-yandex-market-pro&utm_content=settings-page&utm_term=skip-vendor"
				target="_blank">%s</a>',
						__( 'Only products of these brands will be added to the feed', 'yml-for-yandex-market-pro' ),
						__( 'The source of brands is configured on the tab', 'yml-for-yandex-market-pro' ),
						__( 'Attribute settings', 'yfym' ),
						__( 'Read more', 'yml-for-yandex-market-pro' )
					),
					'placeholder' => __( 'Enter brand names separated by semicolons', 'yml-for-yandex-market-pro' ),
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_if_no_vendor',
				'def_val' => 'skip',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'If the product does not have a brand', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'This option only works if you have filled in one of the two fields above',
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'skip', 'text' => __( 'Skip', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'dont_skip', 'text' => __( 'Do not skip', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_add_only_product_ids',
				'def_val' => '',
				'mark' => 'public',
				'required' => false,
				'type' => 'textarea',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Export products by id', 'yml-for-yandex-market-pro' ),
					'desc' => __( 'Only products from these ids are will be add to the feed', 'yml-for-yandex-market-pro' ),
					'placeholder' => __( 'Enter products ids separated by semicolons', 'yml-for-yandex-market-pro' ),
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_compare',
				'def_val' => '>=',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Export only products', 'yml-for-yandex-market-pro' ),
					'desc' => '',
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => '>=', 'text' => __( 'Expensively', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => '<', 'text' => __( 'Cheaper', 'yml-for-yandex-market-pro' ) ]
					],
					'tr_class' => 'ip2vk_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_compare_value',
				'def_val' => '-1',
				'mark' => 'public',
				'required' => false,
				'type' => 'number',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => '',
					'desc' => '',
					'placeholder' => '0',
					'min' => '-1',
					'step' => '0.02'
				]
			],
			[
				'opt_name' => 'y4ymp_stock_quantity_compare',
				'def_val' => '>',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Export only products the stock quantity of which', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '%s %s',
						__(
							'This option only works for products that have the "stock management" checkbox selected',
							'yml-for-yandex-market-pro'
						),
						__(
							'It is recommended to enable stock management for all products, otherwise this filter may not work correctly',
							'yml-for-yandex-market-pro'
						)
					),
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => '>', 'text' => __( 'more', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => '<', 'text' => __( 'less', 'yml-for-yandex-market-pro' ) ]
					],
					'tr_class' => 'ip2vk_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_stock_quantity_compare_value',
				'def_val' => '-1',
				'mark' => 'public',
				'required' => false,
				'type' => 'number',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => '',
					'desc' => __(
						'Set to -1 to not use this filter',
						'yml-for-yandex-market-pro'
					),
					'placeholder' => '-1',
					'min' => '-1',
					'step' => '1'
				]
			],
			[
				'opt_name' => 'y4ymp_stock_quantity_compare_value_max',
				'def_val' => '999999',
				'mark' => 'public',
				'required' => false,
				'type' => 'number',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => '',
					'desc' => __(
						'Set to 999999 to not use this filter',
						'yml-for-yandex-market-pro'
					),
					'placeholder' => '999999',
					'min' => '-1',
					'step' => '1'
				]
			],
			[
				'opt_name' => 'y4ymp_manage_stock_off',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Export only products for which enable stock management', 'yml-for-yandex-market-pro' ),
					'desc' => '',
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_one_variable',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Upload only the first variation', 'yml-for-yandex-market-pro' ),
					'desc' => '',
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_del_tags_atr',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Remove attributes from tags', 'yml-for-yandex-market-pro' ),
					'desc' => '',
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'on', 'text' => __( 'Enabled', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_excl_thumb',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Do not unload the image of the main product', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						"The image specified as the product's main image will not be included in the feed",
						'yml-for-yandex-market-pro'
					),
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market-pro' ) ]
					],
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_add_from_prod_gallery',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => true,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Add images from the general gallery', 'yml-for-yandex-market-pro' ),
					'desc' => __( 'Add images from the general gallery to the images of variations', 'yml-for-yandex-market-pro' ),
					'woo_attr' => false,
					'key_value_arr' => [
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market-pro' ) ]
					]
				]
			],
			[
				'opt_name' => 'y4ymp_num_pic',
				'def_val' => '9',
				'mark' => 'public',
				'required' => false,
				'type' => 'number',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Number of images', 'yml-for-yandex-market-pro' ),
					'desc' => __(
						'The maximum number of images that will be displayed in the feed from the product gallery',
						'yml-for-yandex-market-pro'
					),
					'min' => '0',
					'max' => '20',
					'placeholder' => '9',
					'step' => '1'
				]
			],

			[
				'opt_name' => 'y4ymp_do',
				'def_val' => 'exclude',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'Filtering by category', 'yml-for-yandex-market-pro' ),
					'desc' => '',
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [
						[ 'value' => 'include', 'text' => __( 'Export only', 'yml-for-yandex-market-pro' ) ],
						[ 'value' => 'exclude', 'text' => __( 'Exclude', 'yml-for-yandex-market-pro' ) ]
					],
					'tr_class' => 'y4ym_tr'
				]
			],
			[
				'opt_name' => 'y4ymp_exclude_cat_arr',
				'def_val' => '',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => '',
					'desc' => __( 'products from these categories and tags', 'yml-for-yandex-market-pro' ),
					'woo_attr' => false,
					'categories_arr' => true,
					'tags_arr' => true,
					'multiple' => true,
					'size' => '8',
					'default_value' => false
				]
			],
			[
				'opt_name' => 'y4ymp_categories_list',
				'def_val' => 'full',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'filtration_tab',
				'data' => [
					'label' => __( 'List of categories in the feed header', 'yml-for-yandex-market-pro' ),
					'desc' => sprintf( '%s "%s" %s.<br/>*%s "%s" %s.',
						__( 'The setting only works if you selected', 'yml-for-yandex-market-pro' ),
						__( 'Export only', 'yml-for-yandex-market-pro' ),
						__( 'in the select above', 'yml-for-yandex-market-pro' ),
						__( 'Note that when selecting', 'yml-for-yandex-market-pro' ),
						__( 'Only selected categories', 'yml-for-yandex-market-pro' ),
						__( 'the nesting of categories is ignored', 'yml-for-yandex-market-pro' )
					),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [
						[ 'value' => 'full', 'text' => __( 'Full', 'yml-for-yandex-market-pro' ) ],
						[
							'value' => 'selected',
							'text' => __( 'Only selected categories', 'yml-for-yandex-market-pro' ) . '*'
						]
					]
				]
			]
		];

		// Если активен плагин WooCommerce Multi Inventory & Warehouses
		if ( class_exists( 'WooCommerce_Multi_Inventory' ) ) {
			array_push( $data_arr,
				[
					'opt_name' => 'y4ymp_inventories',
					'def_val' => 'disabled',
					'mark' => 'public',
					'required' => false,
					'type' => 'select',
					'tab' => 'filtration_tab',
					'data' => [
						'label' => __( 'Warehouse', 'yml-for-yandex-market-pro' ),
						'desc' => sprintf( '%s <strong>%s</strong> %s. %s',
							__( 'You are using the', 'yml-for-yandex-market-pro' ),
							'WooCommerce Multi Inventory & Warehouses',
							__( 'plugin', 'yml-for-yandex-market-pro' ),
							__(
								'You can choose which warehouse information will be displayed in the feed',
								'yml-for-yandex-market-pro'
							)
						),
						'woo_attr' => false,
						'default_value' => false,
						'key_value_arr' => [
							[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ]
						]
					]
				]
			);
		}

		return $data_arr;

	}

}