<?php

/**
 * The class hoocked YML for Yandex Market generation.
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 * @version    6.0.15 (28-11-2025)
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/admin
 */

/**
 * The class hoocked YML for Yandex Market generation.
 *
 * Depends on: 
 *   - classes: 
 *   - functions: `common_option_get`, `common_option_upd`
 *   - constants: `Y4YMP_BASIC_PLUGIN_VERSION`.
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/admin
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
final class Y4YMP_Generation_Hoocked {

	/**
	 * The class hoocked YML for Yandex Market generation.
	 */
	public function __construct() {

		$this->init_hooks();
		$this->init_classes();

	}

	/**
	 * Initialization classes.
	 * 
	 * @return void
	 */
	public function init_classes() {
		return;
	}

	/**
	 * Initialization hooks.
	 * 
	 * @return void
	 */
	public function init_hooks() {

		add_filter( 'y4ym_f_after_variable_offer_stop_flag', [ $this, 'change_stop_flag' ], 10, 3 );
		add_action( 'y4ym_f_skip_flag', [ $this, 'change_skip_flag' ], 10, 3 );
		add_action( 'y4ym_f_skip_flag_variable', [ $this, 'change_skip_flag_variable' ], 10, 3 );

		// фильтрация по брендам
		add_filter( 'y4ym_f_simple_skip_vendor_reason', [ $this, 'simple_variable_skip_vendor_reason' ], 10, 3 );
		add_filter( 'y4ym_f_variable_skip_vendor_reason', [ $this, 'simple_variable_skip_vendor_reason' ], 10, 3 );

		// фильтрация по категориям
		add_filter( 'y4ym_f_skip_flag_category', [ $this, 'change_skip_flag_category' ], 10, 3 );
		add_filter( 'y4ym_f_all_parent_flag', [ $this, 'change_all_parent_flag' ], 10, 2 );
		add_filter( 'y4ym_f_category_product_skip_flag', [ $this, 'change_category_product_skip_flag' ], 10, 3 );

		add_filter( 'y4ym_f_query_args', [ $this, 'change_args_query' ], 10, 3 );
		add_filter( 'y4ym_f_args_terms_arr', [ $this, 'change_args_terms_arr' ], 10, 2 );
		add_filter( 'y4ym_f_append_categories', [ $this, 'append_categories' ], 10, 2 );

		add_filter( 'y4ym_f_simple_tag_picture', [ $this, 'add_pic_simple_offer' ], 10, 3 );
		add_filter( 'y4ym_f_variable_tag_picture', [ $this, 'add_pic_variable_offer' ], 10, 4 );

		add_action( 'y4ym_f_simple_price', [ $this, 'change_simple_price' ], 10, 3 );
		add_action( 'y4ym_f_variable_price', [ $this, 'change_variable_price' ], 10, 3 );

		add_filter( 'y4ym_f_simple_tag_value_url', [ $this, 'change_simple_url' ], 10, 3 );
		add_filter( 'y4ym_f_variable_tag_value_url', [ $this, 'change_variable_url' ], 10, 3 );

		add_action( 'y4ym_f_simple_tag_value_categoryid', [ $this, 'simple_tag_value_categoryid' ], 10, 3 );
		add_action( 'y4ym_f_variable_tag_value_categoryid', [ $this, 'variable_tag_value_categoryid' ], 10, 3 );

		add_action( 'y4ym_f_simple_tag_value_count', [ $this, 'simple_tag_value_count' ], 10, 3 );
		add_action( 'y4ym_f_variable_tag_value_count', [ $this, 'variable_tag_value_count' ], 10, 3 );
		add_action( 'y4ym_f_simple_tag_value_amount', [ $this, 'simple_tag_value_count' ], 10, 3 );
		add_action( 'y4ym_f_variable_tag_value_amount', [ $this, 'variable_tag_value_count' ], 10, 3 );
		add_action( 'y4ym_f_simple_tag_value_qty', [ $this, 'simple_tag_value_count' ], 10, 3 );
		add_action( 'y4ym_f_variable_tag_value_qty', [ $this, 'variable_tag_value_count' ], 10, 3 );

		add_filter( 'y4ym_f_simple_tag_value_name', [ $this, 'change_simple_name' ], 10, 3 );
		add_filter( 'y4ym_f_variable_tag_value_name', [ $this, 'change_variable_name' ], 10, 3 );

		add_filter( 'y4ym_f_simple_tag_value_description', [ $this, 'change_simple_description' ], 10, 4 );
		add_filter( 'y4ym_f_variable_tag_value_description', [ $this, 'change_variable_description' ], 10, 4 );

		add_filter( 'y4ym_f_append_simple_offer', [ $this, 'append_simple_offer' ], 10, 3 );
		add_filter( 'y4ym_f_append_variable_offer', [ $this, 'append_variable_offer' ], 10, 3 );

	}

	/**
	 * Return Stop Flag value.
	 * 
	 * Function for `y4ym_f_after_variable_offer_stop_flag` action-hook.
	 * 
	 * @param bool $stop_flag
	 * @param array $data_arr keys: `i`, `variation_count`, `product`, `offer`.
	 * @param string $feed_id
	 * 
	 * @return bool
	 */
	public function change_stop_flag( $stop_flag, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $stop_flag, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $stop_flag;
		}
		$one_variable = common_option_get(
			'y4ymp_one_variable',
			'disabled',
			$feed_id,
			'y4ym'
		);
		if ( $one_variable === 'enabled' ) {
			return true;
		} else {
			return $stop_flag;
		}

	}

	/**
	 * Add skip flag.
	 * 
	 * Function for `y4ym_f_skip_flag` action-hook.
	 * 
	 * @param bool $skip_flag
	 * @param array $data_arr keys: `product`, `catid`.
	 * @param string $feed_id
	 * 
	 * @return mixed
	 */
	public function change_skip_flag( $skip_flag, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $skip_flag, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $skip_flag;
		}

		$product = $data_arr['product'];

		// выгрузка только из YML-наборов
		$whot_export = common_option_get(
			'y4ym_whot_export',
			'all',
			$feed_id,
			'y4ym'
		);
		if ( $whot_export === 'vygruzhat' ) {
			if ( get_post_meta( $product->get_id(), 'vygruzhat', true ) === 'enabled' ) {
				// товар добавлен в YML-набор
			} else {
				$skip_vendor_reason = sprintf( '%s',
					__( 'The product is not included in the YML kit', 'yml-for-yandex-market-pro' )
				);
				return $skip_vendor_reason;
			}
		} else if ( $whot_export === 'collections' ) {
			$collections_arr = get_the_terms( $product->get_id(), 'yfym_collection' );
			if ( false === $collections_arr ) {
				$skip_vendor_reason = sprintf( '%s',
					__( 'The product has not been added to any Collection', 'yml-for-yandex-market-pro' )
				);
				return $skip_vendor_reason;
			}
		}

		// принудительное удаление из всех фидов
		if ( get_post_meta( $product->get_id(), 'yfymp_removefromxml', true ) === 'enabled' ) {
			$skip_vendor_reason = sprintf( '%s "%s" %s',
				__( 'For this product', 'yml-for-yandex-market-pro' ),
				__( 'For this product forcefully remove product from all YML feeds is enabled', 'yml-for-yandex-market-pro' ),
				__( 'is enabled', 'yml-for-yandex-market-pro' )
			);
			return $skip_vendor_reason;
		}

		// принудительное удаление из выбранных фидов
		if ( get_post_meta( $product->get_id(), 'yfymp_removefromthisyml_arr', true ) !== '' ) {
			$removefromthisyml_arr = maybe_unserialize(
				get_post_meta( $product->get_id(), 'yfymp_removefromthisyml_arr', true )
			);
			if ( is_array( $removefromthisyml_arr ) && in_array( $feed_id, $removefromthisyml_arr ) ) {
				$skip_vendor_reason = sprintf( '%s "%s" %s',
					__( 'For this product', 'yml-for-yandex-market-pro' ),
					__( 'Forcefully remove product from selected YML feeds', 'yml-for-yandex-market-pro' ),
					__( 'is enabled', 'yml-for-yandex-market-pro' )
				);
				return $skip_vendor_reason;
			}
		}

		// только товары с этим ID
		$add_only_product_ids = common_option_get(
			'y4ymp_add_only_product_ids',
			'',
			$feed_id,
			'y4ym'
		);
		if ( ! empty( $add_only_product_ids ) ) {
			$add_only_product_ids_arr = explode( ";", $add_only_product_ids );
			for ( $i = 0; $i < count( $add_only_product_ids_arr ); $i++ ) {
				$add_only_product_ids_arr[ $i ] = trim( $add_only_product_ids_arr[ $i ] );
				$add_only_product_ids_arr[ $i ] = (int) $add_only_product_ids_arr[ $i ];
			}

			if ( ! in_array( $product->get_id(), $add_only_product_ids_arr ) ) {
				$skip_vendor_reason = sprintf( '%s "%s" %s',
					__( 'Filtering by products ids is enabled and', 'yml-for-yandex-market-pro' ),
					$product->get_id(),
					__( 'products is not included in the list of permitted', 'yml-for-yandex-market-pro' )
				);
				return $skip_vendor_reason;
			}
		}

		// принудительное удаление из всех фидов
		if ( empty( get_post_meta( $product->get_id(), 'yfymp_removefromthisyml_arr', true ) )
			|| get_post_meta( $product->get_id(), 'yfymp_removefromthisyml_arr', true ) == '' ) {
		} else {
			$removefromthisyml_arr = get_post_meta( $product->get_id(), 'yfymp_removefromthisyml_arr', true );
			if ( in_array( $feed_id, $removefromthisyml_arr ) ) {
				return sprintf( '%s "%s"',
					__( 'Selected', 'yml-for-yandex-market-pro' ),
					__( 'Forcefully remove product from selected YML feeds', 'yml-for-yandex-market-pro' )
				);
			}
		}

		if ( $product->is_type( 'simple' ) ) {

			// выгрузка по цене
			$compare = common_option_get(
				'y4ymp_compare',
				'>=',
				$feed_id,
				'y4ym'
			);
			$compare_value = (float) common_option_get(
				'y4ymp_compare_value',
				'-1',
				$feed_id,
				'y4ym'
			);
			if ( $compare === '>=' ) {
				// дороже
				if ( (float) $compare_value > (float) $product->get_price() ) {
					$skip_vendor_reason = sprintf( '%s. %s: %s < %s',
						__( 'Filtering of products by price is enabled', 'yml-for-yandex-market-pro' ),
						__( 'The product price', 'yml-for-yandex-market-pro' ),
						(float) $product->get_price(),
						(float) $compare_value
					);
					return $skip_vendor_reason;
				}
			} else {
				// дешевле
				if ( (float) $compare_value <= (float) $product->get_price() ) {
					$skip_vendor_reason = sprintf( '%s. %s: %s >= %s',
						__( 'Filtering of products by price is enabled', 'yml-for-yandex-market-pro' ),
						__( 'The product price', 'yml-for-yandex-market-pro' ),
						(float) $product->get_price(),
						(float) $compare_value
					);
					return $skip_vendor_reason;
				}
			}

			// фильтрация по наличию
			$min = (int) common_option_get(
				'y4ymp_stock_quantity_compare_value',
				-1,
				$feed_id,
				'y4ym'
			);
			$max = (int) common_option_get(
				'y4ymp_stock_quantity_compare_value_max',
				999999,
				$feed_id,
				'y4ym'
			);

			if ( $min > 0 || $max > 0 ) {
				if ( $max == 0 ) {
					$max = 999999;
				}
				if ( true === $product->get_manage_stock() ) { // включено управление запасом
					$stock_quantity = (int) $product->get_stock_quantity(); // тк по дефолту тут может быть null

					if ( $stock_quantity > $min && $stock_quantity < $max ) {
					} else {
						return sprintf( '%1$s = %2$d. %3$s (%4$d > %2$d < %5$d)',
							__( 'Stock quantity', 'yml-for-yandex-market-pro' ),
							$stock_quantity,
							__( 'Condition not met', 'yml-for-yandex-market-pro' ),
							$min,
							$max
						);
					}
				} else {
					$manage_stock_off = common_option_get(
						'y4ymp_manage_stock_off',
						'disabled',
						$feed_id,
						'y4ym'
					);
					if ( $manage_stock_off === 'enabled' ) {
						// пропуск товаров для которых не включили управление запасами
						return __( 'Stock quantity is not set', 'yml-for-yandex-market-pro' );
					}
					return $skip_flag;
				}
			}

		} // end if ( $product->is_type( 'simple' ) )

		if ( isset( $data_arr['catid'] ) ) {

			// TODO: в будущем сюда возможно вставить фильтрацию на уровне категории

		}
		return $skip_flag;

	}

	/**
	 * Add skip flag for variable products.
	 * 
	 * Function for `y4ym_f_skip_flag_variable` action-hook.
	 * 
	 * @param bool $skip_flag
	 * @param array $data_arr keys: `product`, `offer`, `catid`.
	 * @param string $feed_id
	 * 
	 * @return mixed
	 */
	public function change_skip_flag_variable( $skip_flag, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $skip_flag, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $skip_flag;
		}

		$product = $data_arr['product'];
		$offer = $data_arr['offer'];

		// выгрузка по цене
		$compare = common_option_get(
			'y4ymp_compare',
			'>=',
			$feed_id,
			'y4ym'
		);
		$compare_value = (float) common_option_get(
			'y4ymp_compare_value',
			'-1',
			$feed_id,
			'y4ym'
		);
		if ( $compare === '>=' ) {
			// дороже
			if ( (float) $compare_value > (float) $offer->get_price() ) {
				$skip_vendor_reason = sprintf( '%s. %s: %s < %s',
					__( 'Filtering of products by price is enabled', 'yml-for-yandex-market-pro' ),
					__( 'The variation price', 'yml-for-yandex-market-pro' ),
					$offer->get_price(),
					$compare_value
				);
				return $skip_vendor_reason;
			}
		} else {
			// дешевле
			if ( (float) $compare_value <= (float) $offer->get_price() ) {
				$skip_vendor_reason = sprintf( '%s. %s: %s >= %s',
					__( 'Filtering of products by price is enabled', 'yml-for-yandex-market-pro' ),
					__( 'The variation price', 'yml-for-yandex-market-pro' ),
					$offer->get_price(),
					$compare_value
				);
				return $skip_vendor_reason;
			}
		}

		// фильтрация по наличию
		$min = (int) common_option_get(
			'y4ymp_stock_quantity_compare_value',
			-1,
			$feed_id,
			'y4ym'
		);
		$max = (int) common_option_get(
			'y4ymp_stock_quantity_compare_value_max',
			999999,
			$feed_id,
			'y4ym'
		);

		if ( $min > 0 || $max > 0 ) {
			if ( $max == 0 ) {
				$max = 999999;
			}
			if ( true === $offer->get_manage_stock() ) { // включено управление запасом
				$stock_quantity = (int) $offer->get_stock_quantity(); // тк по дефолту тут может быть null
				if ( $stock_quantity > $min && $stock_quantity < $max ) {
				} else {
					return sprintf( '%1$s = %2$d. %3$s (%4$d > %2$d < %5$d)',
						__( 'Stock quantity', 'yml-for-yandex-market-pro' ),
						$stock_quantity,
						__( 'Condition not met', 'yml-for-yandex-market-pro' ),
						$min,
						$max
					);
				}
			} else if ( true == $product->get_manage_stock() ) {
				$stock_quantity = (int) $offer->get_stock_quantity(); // тк по дефолту тут может быть null
				if ( $stock_quantity > $min && $stock_quantity < $max ) {
				} else {
					return sprintf( '%1$s = %2$d. %3$s (%4$d > %2$d < %5$d)',
						__( 'Stock quantity', 'yml-for-yandex-market-pro' ),
						$stock_quantity,
						__( 'Condition not met', 'yml-for-yandex-market-pro' ),
						$min,
						$max
					);
				}
			} else {
				$manage_stock_off = common_option_get(
					'y4ymp_manage_stock_off',
					'disabled',
					$feed_id,
					'y4ym'
				);
				if ( $manage_stock_off === 'enabled' ) {
					// пропуск товаров для которых не включили управление запасами
					return __( 'Stock quantity is not set', 'yml-for-yandex-market-pro' );
				}
				return $skip_flag;
			}
		}
		if ( isset( $data_arr['catid'] ) ) {

			// TODO: в будущем сюда возможно вставить фильтрацию на уровне категории

		}
		return $skip_flag;

	}

	/**
	 * Filtering by brand.
	 * 
	 * TODO: Баг фильтрации по брендам. Не срабатывает для правил в которых нет vendor тк сама фильтрация
	 * срабатывает в момент формирования тега vendor
	 * 
	 * Function for `y4ym_f_simple_skip_vendor_reason`, `y4ym_f_variable_skip_vendor_reason`
	 * action-hooks.
	 * 
	 * @param mixed $skip_vendor_reason
	 * @param array $dara_arr keys: `product`, `vendor_name` and `offer` (optional).
	 * @param string $feed_id
	 * 
	 * @return mixed
	 */
	public function simple_variable_skip_vendor_reason( $skip_vendor_reason, $dara_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $skip_vendor_reason, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $skip_vendor_reason;
		}

		$if_no_vendor = common_option_get(
			'y4ymp_if_no_vendor',
			'skip',
			$feed_id,
			'y4ym'
		);
		$skip_vendor_names = common_option_get(
			'y4ymp_skip_vendor_names',
			'',
			$feed_id,
			'y4ym'
		);
		$only_vendor_names = common_option_get(
			'y4ymp_only_vendor_names',
			false,
			$feed_id,
			'y4ym'
		);

		// пропуск товаров без брендов
		if ( $if_no_vendor === 'skip' ) {
			if ( empty( $dara_arr['vendor_name'] )
				&& ( ! empty( $skip_vendor_names ) || ! empty( $only_vendor_names ) )
			) {
				$skip_vendor_reason = sprintf( '%s %s.',
					__( 'Filtering by brands is enabled and', 'yml-for-yandex-market-pro' ),
					__( 'this product has no brand', 'yml-for-yandex-market-pro' )
				);
				return $skip_vendor_reason;
			}
		}

		// пропускать эти бренды
		if ( ! empty( $skip_vendor_names ) ) {
			$skip_vendor_names_arr = explode( ";", $skip_vendor_names );
			for ( $i = 0; $i < count( $skip_vendor_names_arr ); $i++ ) {
				$skip_vendor_names_arr[ $i ] = trim( $skip_vendor_names_arr[ $i ] );
			}
			if ( in_array( $dara_arr['vendor_name'], $skip_vendor_names_arr ) ) {
				$skip_vendor_reason = sprintf( '%s "%s" %s',
					__( 'Filtering by brands is enabled and', 'yml-for-yandex-market-pro' ),
					$dara_arr['vendor_name'],
					__( 'brand is not included in the list of permitted', 'yml-for-yandex-market-pro' )
				);
			}
		}

		// выгружать только эти бренды
		if ( ! empty( $only_vendor_names ) ) {
			$only_vendor_names_arr = explode( ";", $only_vendor_names );
			for ( $i = 0; $i < count( $only_vendor_names_arr ); $i++ ) {
				$only_vendor_names_arr[ $i ] = trim( $only_vendor_names_arr[ $i ] );
			}
			if ( ! in_array( $dara_arr['vendor_name'], $only_vendor_names_arr ) ) {
				$skip_vendor_reason = sprintf( '%s "%s" %s',
					__( 'Filtering by brands is enabled and', 'yml-for-yandex-market-pro' ),
					$dara_arr['vendor_name'],
					__( 'brand is not included in the list of permitted', 'yml-for-yandex-market-pro' )
				);
			}
		}

		return $skip_vendor_reason;

	}

	/**
	 * Проспускает категории - фильтр работает при формировании списка категорий.
	 * 
	 * Function for `y4ym_f_skip_flag_category` action-hook.
	 * 
	 * @param bool $skip_flag_category
	 * @param array $data_arr keys: `terms`, `terms`.
	 * @param string $feed_id
	 * 
	 * @return bool
	 */
	public function change_skip_flag_category( $skip_flag_category, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $skip_flag_category, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $skip_flag_category;
		}

		$term = $data_arr['term'];
		$do = common_option_get(
			'y4ymp_do',
			'exclude',
			$feed_id,
			'y4ym'
		);
		$categories_list = common_option_get(
			'y4ymp_categories_list',
			'full',
			$feed_id,
			'y4ym'
		);
		if ( $do === 'include' && $categories_list === 'selected' ) {
			$opt_name = sprintf( 'y4ymp_exclude_cat_arr%s', $feed_id );
			$only_this_cat_arr = maybe_unserialize(
				common_option_get( $opt_name, [], '0' ) // ! именно '0'
			);
			if ( is_array( $only_this_cat_arr ) // ? возможно стоит удалить is_array()
				&& ! in_array( (string) $term->term_id, $only_this_cat_arr ) ) {
				$skip_flag_category = true;
			}
		}

		return $skip_flag_category;

	}

	/**
	 * Делает все категории категориями первого уровня - фильтр работает при 
	 * формировании списка категорий.
	 * 
	 * Function for `y4ym_f_all_parent_flag` action-hook.
	 * 
	 * @param bool $all_parent_flag
	 * @param string $feed_id
	 * 
	 * @return bool
	 */
	public function change_all_parent_flag( $all_parent_flag, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $all_parent_flag, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $all_parent_flag;
		}

		$categories_list = common_option_get(
			'y4ymp_categories_list',
			'full',
			$feed_id,
			'y4ym'
		);
		if ( $categories_list === 'selected' ) {
			return true;
		} else {
			return $all_parent_flag;
		}

	}

	/**
	 * Функция нужна, чтобы товарам не прописывались категории, выгрузка которых отключена.
	 * 
	 * Function for `y4ym_f_category_product_skip_flag` action-hook.
	 * 
	 * @param bool $skip_flag_category
	 * @param array $data_arr keys: `product`, `offer`, term_id`, feed_category_id`.
	 * @param string $feed_id
	 * 
	 * @return bool
	 */
	public function change_category_product_skip_flag( $skip_flag_category, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $skip_flag_category, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $skip_flag_category;
		}

		$opt_name = sprintf( 'y4ymp_exclude_cat_arr%s', $feed_id );

		$only_this_cat_arr = maybe_unserialize(
			common_option_get( $opt_name, [], '0' ) // ! именно '0'
		);

		$categories_list = common_option_get(
			'y4ymp_categories_list',
			'full',
			$feed_id,
			'y4ym'
		);
		$do = common_option_get(
			'y4ymp_do',
			'exclude',
			$feed_id,
			'y4ym'
		);
		if ( $categories_list !== 'selected' && $do === 'include' ) {
			// если в шапку выводить полный список категорий и выбрано "Эксп-ть только"
			return $skip_flag_category;
		}

		if ( ! empty( $only_this_cat_arr ) ) {
			// если мультиселект "Фильтрация по категориям" не пуст

			if ( $do === 'include' ) {
				// если выбрано "Эксп-ть только"
				if ( ! in_array( (string) $data_arr['term_id'], $only_this_cat_arr ) ) {
					$skip_flag_category = true;
				}
			} else {
				// если выбрано "Исключить"
				if ( in_array( (string) $data_arr['term_id'], $only_this_cat_arr ) ) {
					$skip_flag_category = true;
				}
			}

			if ( true === $skip_flag_category && $do === 'include' ) {
				$tags_as_cat = common_option_get(
					'y4ymp_tags_as_cat',
					'disabled',
					$feed_id,
					'y4ym'
				);
				// если включена опция теги в качестве категорий
				if ( $tags_as_cat !== 'disabled' ) {
					$product_tag_arr = get_the_terms( $data_arr['product']->get_id(), 'product_tag' );
					if ( ! empty( $product_tag_arr ) ) {
						foreach ( $product_tag_arr as $cur_term ) {
							if ( in_array( (string) $cur_term->term_id, $only_this_cat_arr ) ) {
								$skip_flag_category = false;
							}
						}
					}
				}
			}
		}

		return $skip_flag_category;

	}

	/**
	 * Функция отвечает за исключение/включение товаров по категориям и по цене 
	 * на этапе формирования запроса к БД. 
	 * 
	 * Function for `y4ym_f_query_arg` filter-hook.
	 * 
	 * @param array $args_arr
	 * @param string $feed_id
	 * 
	 * @return array
	 */
	public function change_args_query( $args_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $args_arr, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $args_arr;
		}

		$opt_name = sprintf( 'y4ymp_exclude_cat_arr%s', $feed_id );
		$params_arr = maybe_unserialize(
			common_option_get( $opt_name, [], '0' ) // ! именно '0'
		);
		$do = common_option_get(
			'y4ymp_do',
			'include',
			$feed_id,
			'y4ym'
		);
		if ( empty( $params_arr ) ) {
			return $args_arr;
		}
		if ( $do === 'include' ) {
			$args_arr['tax_query'] = [
				'relation' => 'OR',
				[
					'taxonomy' => 'product_cat',
					'field' => 'id',
					'terms' => $params_arr,
					'operator' => 'IN'
				],
				[
					'taxonomy' => 'product_tag',
					'field' => 'id',
					'terms' => $params_arr,
					'operator' => 'IN'
				]
			];
		} else {
			$args_arr['tax_query'] = [
				'relation' => 'AND',
				[
					'taxonomy' => 'product_cat',
					'field' => 'id',
					'terms' => $params_arr,
					'operator' => 'NOT IN'
				],
				[
					'taxonomy' => 'product_tag',
					'field' => 'id',
					'terms' => $params_arr,
					'operator' => 'NOT IN'
				]
			];
		}

		// ! удаление из всех фидов
		// ? эта опция ооочень сильно утяжеляет запрос
		// $args_arr['meta_query'] = [ 
		// 	'relation' => 'OR', // Используем OR для объединения условий
		// 	[ 
		// 		'key' => 'yfymp_removefromxml',
		// 		'value' => 'disabled',
		// 		'compare' => '='
		// 	],
		// 	[ 
		// 		'key' => 'yfymp_removefromxml',
		// 		'compare' => 'NOT EXISTS'
		// 	]
		// ];

		return $args_arr;

	}

	/**
	 * Функция позволяет изменить список категорий в шапке фида. 
	 * 
	 * Function for `y4ym_f_args_terms_arr` filter-hook.
	 * 
	 * @param array $args_terms_arr
	 * @param string $feed_id
	 * 
	 * @return array
	 */
	public function change_args_terms_arr( $args_terms_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $args_terms_arr, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $args_terms_arr;
		}

		// $opt_name = sprintf( 'y4ymp_exclude_cat_arr%s', $feed_id );
		// $only_this_cat_arr = maybe_unserialize(
		//	common_option_get( $opt_name, [], '0' ) // ! именно '0'
		// );
		$do = common_option_get(
			'y4ymp_do',
			'exclude',
			$feed_id,
			'y4ym'
		);
		if ( empty( $params_arr ) ) {
			return $args_terms_arr;
		}
		if ( $do == 'include' ) {
			// ? подумать над тем, как собрать еще и id-шники дочерних категорий
			// $args_terms_arr['include'] = $params_arr;
		} else {
			$args_terms_arr['exclude_tree'] = $params_arr;
		}
		return $args_terms_arr;

	}

	/**
	 * Функция позволяет изменить/добавить список категорий в шапке фида. 
	 * 
	 * Function for `y4ym_f_append_categories` filter-hook.
	 * 
	 * @param string $result_xml
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function append_categories( $result_xml, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $result_xml, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $result_xml;
		}

		$tags_as_cat = common_option_get(
			'y4ymp_tags_as_cat',
			'disabled',
			$feed_id,
			'y4ym'
		);
		$disabled_ymarket = common_option_get(
			'y4ymp_disabled_ymarket',
			'disabled',
			$feed_id,
			'y4ym'
		);

		if ( $disabled_ymarket === 'enabled' ) {
			/* дерево категорий как на маркете */
			$terms = get_terms( [
				'hide_empty' => 0,
				'orderby' => 'name',
				'taxonomy' => 'ymarket'
			] );
			$count = count( $terms );
			if ( $count > 0 ) {
				foreach ( $terms as $term ) {
					$attr_arr = [ 'id' => $term->term_id ];
					if ( $term->parent !== 0 ) {
						$attr_arr['parentId'] = $term->parent;
					}
					// ! $add_attr = apply_filters( 'yfymp_add_category_attr_filter', $add_attr, $terms, $term, $feed_id );
					$result_xml .= new Y4YM_Get_Paired_Tag(
						'category',
						$term->name,
						$attr_arr
					);
				}
			}
			/* end дерево категорий как на маркете */
		}

		if ( $tags_as_cat === 'on_cat' || $tags_as_cat === 'on' ) {
			/* метки в качестве категорий */
			$args = [
				'taxonomy' => 'product_tag',
				'hide_empty' => true // скроем метки-таксономии без тегов
			];
			$terms_product_tag = get_terms( $args );
			if ( $terms_product_tag && ! is_wp_error( $terms_product_tag ) ) {
				foreach ( $terms_product_tag as $term ) {
					$result_xml .= new Y4YM_Get_Paired_Tag(
						'category',
						$term->name,
						[ 'id' => $term->term_id ]
					);
				}
			}
		}

		// ! $result_xml = apply_filters( 'yfymp_append_categories_filter', $result_xml, $feed_id );
		return $result_xml;

	}

	/**
	 * Adds additional photos to the simple product. 
	 * 
	 * Function for `y4ym_f_simple_tag_picture` filter-hook.
	 * 
	 * @param string $picture_yml
	 * @param array $product keys: `product`, `size_pic`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function add_pic_simple_offer( $result_xml, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $result_xml, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $result_xml;
		}

		$product = $data_arr['product'];

		$num_pic = (int) common_option_get(
			'y4ymp_num_pic',
			9,
			$feed_id,
			'y4ym'
		);
		$excl_thumb = common_option_get(
			'y4ymp_excl_thumb',
			'disabled',
			$feed_id,
			'y4ym'
		);

		// если запрет на выгрузку миниатюры - очистим picture_yml
		if ( $excl_thumb === 'enabled' ) {
			$result_xml = '';
		}
		// если не нужны допфотки
		if ( $num_pic < 1 ) {
			return $result_xml;
		}

		$no_default_png_products = common_option_get(
			'y4ym_no_default_png_products',
			'disabled',
			$feed_id,
			'y4ym'
		);
		$attachment_ids = $product->get_gallery_image_ids();
		$i = (int) 1;
		foreach ( $attachment_ids as $attachment_id ) {
			$pic_gal = wp_get_attachment_image_src( $attachment_id, $data_arr['size_pic'], true );
			$pic_gal_url = $pic_gal[0]; // урл оригинала картинки в галерее товара */
			$tag_value = get_from_url( $pic_gal_url );
			if ( $no_default_png_products === 'enabled' ) {
				// включён пропуск default.png из фида
				if ( false !== strpos( $tag_value, 'default.' ) ) {
					continue;
				}
			}
			$result_xml .= $this->skip_gif( 'picture', $tag_value );

			if ( $i === $num_pic ) {
				break;
			} else {
				$i++;
			}
		}

		return $result_xml;

	}

	/**
	 * Adds additional photos to the variable product. 
	 * 
	 * Function for `y4ym_f_variable_tag_picture` filter-hook.
	 * 
	 * @param string $picture_yml
	 * @param array $data_arr keys: `product`, `offer`, `size_pic`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function add_pic_variable_offer( $result_xml, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $result_xml, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $result_xml;
		}

		$product = $data_arr['product'];
		$offer = $data_arr['offer'];

		$num_pic = (int) common_option_get(
			'y4ymp_num_pic',
			9,
			$feed_id,
			'y4ym'
		);
		$excl_thumb = common_option_get(
			'y4ymp_excl_thumb',
			'disabled',
			$feed_id,
			'y4ym'
		);

		// если запрет на выгрузку миниатюры - очистим picture_yml
		if ( $excl_thumb === 'enabled' ) {
			$result_xml = '';
		}
		// если не нужны допфотки
		if ( $num_pic < 1 ) {
			return $result_xml;
		}

		// id доп.картинок из галлереии вариации
		if ( null === $offer ) {
			$add_attachment_ids = [];
		} else {
			// считываем индивидуальные картинки из галлереии вариации
			$add_attachment_ids = $offer->get_gallery_image_ids();
		}

		$add_pic_from_product_gallery = common_option_get(
			'y4ymp_add_from_prod_gallery',
			'disabled',
			$feed_id,
			'y4ym'
		);
		if ( $add_pic_from_product_gallery === 'enabled' ) {
			$add_attachment_ids = array_merge( $add_attachment_ids, $product->get_gallery_image_ids() );
		}

		if ( class_exists( 'WooProductVariationGallery' ) || class_exists( 'WooProductVariationGalleryPro' ) ) {
			// установлин плагин допкартинок для вариаций
			$offer_id = absint( $offer->get_id() );
			$has_variation_gallery_images = (bool) get_post_meta( $offer_id, 'rtwpvg_images', true );
			if ( $has_variation_gallery_images ) {
				$gallery_images = (array) get_post_meta( $offer_id, 'rtwpvg_images', true );
				$attachment_ids = $gallery_images; // в приоритете галерея вариации
				foreach ( $add_attachment_ids as $attachment_id ) {
					if ( in_array( $attachment_id, $attachment_ids ) ) {
						continue;
					} else {
						$attachment_ids[] = $attachment_id;
					}
				}
			} else {
				$attachment_ids = $add_attachment_ids;
			}
		} else {
			$attachment_ids = $add_attachment_ids;
		}

		$i = 1;
		$no_default_png_products = common_option_get(
			'y4ym_no_default_png_products',
			'disabled',
			$feed_id,
			'y4ym'
		);
		foreach ( $attachment_ids as $attachment_id ) {
			$pic_gal = wp_get_attachment_image_src( $attachment_id, $data_arr['size_pic'], true );
			$pic_gal_url = $pic_gal[0]; // урл оригинала картинки в галерее товара */
			$tag_value = get_from_url( $pic_gal_url );
			if ( $no_default_png_products === 'enabled' ) {
				// включён пропуск default.png из фида
				if ( false !== strpos( $tag_value, 'default.' ) ) {
					continue;
				}
			}
			$result_xml .= $this->skip_gif( 'picture', $tag_value );

			if ( $i === $num_pic ) {
				break;
			} else {
				$i++;
			}
		}

		return $result_xml;

	}

	/**
	 * Skip `gif` and `svg` files.
	 * 
	 * @param string $tag_name
	 * @param string $tag_value
	 * 
	 * @return string
	 */
	private function skip_gif( $tag_name, $tag_value ) {

		// удаляем из фида gif и svg картинки
		$tag_value = get_from_url( $tag_value, 'url' );
		if ( preg_match( '/\.(gif|svg)$/i', $tag_value ) ) {
			// это gif или svg
			$picture_xml = '';
		} else {
			$picture_xml = new Y4YM_Get_Paired_Tag( $tag_name, $tag_value );
		}
		return $picture_xml;

	}

	/**
	 * Change simple price. // TODO: удалить умножение цены (28-11-2025)
	 * 
	 * Function for `y4ym_f_simple_price` filter-hook.
	 * 
	 * @param string $price
	 * @param array $data_arr keys: `product`, `product_category_id`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function change_simple_price( $price, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $price, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $price;
		}

		$price = (float) $price; // ! очень важный момент для php 8 и выше

		$flag_block_price_changes = false;
		$flag_block_price_changes = apply_filters(
			'y4ymp_f_simple_flag_block_price_changes',
			$flag_block_price_changes,
			$data_arr,
			$feed_id
		);
		if ( empty( $price ) || $price == 0 || true === $flag_block_price_changes ) {
			return $price;
		}

		$product = $data_arr['product'];
		$product_category_id = $data_arr['product_category_id'];

		$disabled_for_feeds_arr = get_term_meta(
			$product_category_id,
			'yfymp_disabled_for_feeds',
			true
		);
		if ( ! is_array( $disabled_for_feeds_arr ) ) {
			$disabled_for_feeds_arr = [];
		}

		if ( in_array( $feed_id, $disabled_for_feeds_arr ) ) {
			$multiply_price_value = (float) 0;
			$price_percentage = (float) 0;
			$add_to_price_value = (float) 0;
		} else {
			$multiply_price_value = (float) get_term_meta(
				$product_category_id,
				'yfymp_multiply_price_value',
				true
			);
			$price_percentage = (float) get_term_meta(
				$product_category_id,
				'yfymp_price_percentage',
				true
			);
			$add_to_price_value = (float) get_term_meta(
				$product_category_id,
				'yfymp_add_to_price_value',
				true
			);
		}

		$post_id = $product->get_id();
		$price_post_meta = common_option_get(
			'y4ymp_price_post_meta',
			'',
			$feed_id,
			'y4ym'
		);
		if ( ! empty( $price_post_meta ) ) {
			if ( get_post_meta( $post_id, $price_post_meta, true ) !== '' ) {
				$price = (float) get_post_meta( $post_id, $price_post_meta, true );
			}
		}

		if ( $multiply_price_value === (float) 0 ) {
			$multiply_price_value = (float) common_option_get(
				'y4ymp_multiply_price_value',
				1,
				$feed_id,
				'y4ym'
			);
		}
		if ( $multiply_price_value === (float) 1 || $multiply_price_value === (float) 0 ) {
			// умножение цены не требуется
		} else {
			$price = round( $price * $multiply_price_value, 2 );
		}

		if ( $price_percentage === (float) 0 ) {
			$price_percentage = (float) common_option_get(
				'y4ymp_price_percentage',
				'0',
				$feed_id,
				'y4ym'
			);
		}
		if ( $price_percentage !== 0 ) {
			$price = $price + ( $price_percentage * ( $price / 100 ) );
		}

		if ( $add_to_price_value === (float) 0 ) {
			$add_to_price_value = (float) common_option_get(
				'y4ymp_add_to_price_value',
				0,
				$feed_id,
				'y4ym'
			);
		}
		if ( $add_to_price_value === (float) 0 ) {
			// добавление к цене не требуется
		} else {
			$price = round( $price + $add_to_price_value, 2 );
		}

		$price = $this->round_price( $price, $feed_id );

		return $price;

	}

	/**
	 * Change variable price. // TODO: удалить умножение цены (28-11-2025)
	 * 
	 * Function for `y4ym_f_variable_price` filter-hook.
	 * 
	 * @param string $price
	 * @param array $data_arr keys: `product`, `offer`, `product_category_id`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function change_variable_price( $price, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $price, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $price;
		}

		$price = (float) $price; // ! очень важный момент для php 8 и выше

		$flag_block_price_changes = false;
		$flag_block_price_changes = apply_filters(
			'y4ymp_f_variable_flag_block_price_changes',
			$flag_block_price_changes,
			$data_arr,
			$feed_id
		);
		if ( empty( $price ) || $price == 0 || true === $flag_block_price_changes ) {
			return $price;
		}

		$product = $data_arr['product'];
		$offer = $data_arr['offer'];
		$product_category_id = $data_arr['product_category_id'];

		$disabled_for_feeds_arr = get_term_meta(
			$product_category_id,
			'yfymp_disabled_for_feeds',
			true
		);
		if ( ! is_array( $disabled_for_feeds_arr ) ) {
			$disabled_for_feeds_arr = [];
		}

		if ( in_array( $feed_id, $disabled_for_feeds_arr ) ) {
			$multiply_price_value = (float) 0;
			$price_percentage = (float) 0;
			$add_to_price_value = (float) 0;
		} else {
			$multiply_price_value = (float) get_term_meta(
				$product_category_id,
				'yfymp_multiply_price_value',
				true
			);
			$price_percentage = (float) get_term_meta(
				$product_category_id,
				'yfymp_price_percentage',
				true
			);
			$add_to_price_value = (float) get_term_meta(
				$product_category_id,
				'yfymp_add_to_price_value',
				true
			);
		}

		$price_post_meta = common_option_get(
			'y4ymp_price_post_meta',
			'',
			$feed_id,
			'y4ym'
		);
		if ( ! empty( $price_post_meta ) ) {
			if ( get_post_meta( $product->get_id(), $price_post_meta, true ) !== '' ) {
				$price = (float) get_post_meta( $product->get_id(), $price_post_meta, true );
			}
		}

		if ( $multiply_price_value === (float) 0 ) {
			$multiply_price_value = (float) common_option_get(
				'y4ymp_multiply_price_value',
				1,
				$feed_id,
				'y4ym'
			);
		}

		if ( $multiply_price_value === (float) 1 || $multiply_price_value === (float) 0 ) {
			// умножение цены не требуется
		} else {
			$price = round( $price * $multiply_price_value, 2 );
		}

		if ( $price_percentage === (float) 0 ) {
			$price_percentage = (float) common_option_get(
				'y4ymp_price_percentage',
				'0',
				$feed_id,
				'y4ym'
			);
		}
		if ( $price_percentage !== 0 ) {
			$price = $price + ( $price_percentage * ( $price / 100 ) );
		}

		if ( $add_to_price_value === (float) 0 ) {
			$add_to_price_value = (float) common_option_get(
				'y4ymp_add_to_price_value',
				0,
				$feed_id,
				'y4ym'
			);
		}
		if ( $add_to_price_value === (float) 0 ) {
			// добавление к цене не требуется
		} else {
			$price = round( $price + $add_to_price_value, 2 );
		}

		$price = $this->round_price( $price, $feed_id );

		return $price;

	}

	/**
	 * Round product price.
	 * 
	 * @param float|int $price
	 * @param string|int $feed_id
	 * 
	 * @return float
	 */
	private function round_price( $price, $feed_id ) {

		$round_price_value = common_option_get(
			'y4ymp_round_price_value',
			'hundredths',
			$feed_id,
			'y4ym'
		);
		switch ( $round_price_value ) {
			case 'hundredths':
				$price = round( $price, 2 );
				break;
			case 'integers':
				$price = round( $price, 0 );
				break;
			case 'tens':
				$price = round( $price, -1 );
				break;
			case 'hundreds':
				$price = round( $price, -2 );
				break;
			case 'thousands':
				$price = round( $price, -3 );
				break;
			default:
				$price = round( $price, 2 );
		}
		return (float) $price;

	}

	/**
	 * Change simple url.
	 * 
	 * Function for `y4ym_f_simple_url` filter-hook.
	 * 
	 * @param string $tag_value
	 * @param array $data_arr keys: `product`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function change_simple_url( $tag_value, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $tag_value, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $tag_value;
		}

		$product = $data_arr['product'];
		$category_id = $data_arr['feed_category_id'];

		$use_utm = common_option_get(
			'y4ymp_use_utm',
			'disabled',
			$feed_id,
			'y4ym'
		);
		// utm_source
		$utm_source = common_option_get(
			'y4ymp_utm_source',
			'market.yandex.ru',
			$feed_id,
			'y4ym'
		);
		// utm_medium
		$utm_medium = common_option_get(
			'y4ymp_utm_medium',
			'cpc',
			$feed_id,
			'y4ym'
		);
		$utm_medium = str_replace( '{catid}', $category_id, $utm_medium );
		$utm_medium = str_replace( '{productid}', $product->get_id(), $utm_medium );
		// utm_campaign
		$utm_campaign = common_option_get(
			'y4ymp_utm_campaign',
			'',
			$feed_id,
			'y4ym'
		);
		$utm_campaign = str_replace( '{catid}', $category_id, $utm_campaign );
		$utm_campaign = str_replace( '{productid}', $product->get_id(), $utm_campaign );
		// utm_content
		$utm_content = common_option_get(
			'y4ymp_utm_content',
			'catid_prodid_slug',
			$feed_id,
			'y4ym'
		);
		// utm_term
		$utm_term = common_option_get(
			'y4ymp_utm_term',
			'',
			$feed_id,
			'y4ym'
		);
		if ( $utm_term !== '' ) {
			$utm_term = str_replace( '{catid}', $category_id, $utm_term );
			$utm_term = str_replace( '{productid}', $product->get_id(), $utm_term );
			$utm_term = str_replace( '{productslug}', $product->get_slug(), $utm_term );
		}
		// rs
		$roistat = common_option_get(
			'y4ymp_roistat',
			'',
			$feed_id,
			'y4ym'
		);
		if ( $roistat !== '' ) {
			$roistat = str_replace( '{catid}', $category_id, $roistat );
			$roistat = str_replace( '{productid}', $product->get_id(), $roistat );
		}

		switch ( $utm_content ) {
			case "catid":

				$utm_content_res = $category_id;

				break;
			case "catid_prodid":

				$utm_content_res = sprintf( 'cat%s--prod%s',
					$category_id,
					$product->get_id()
				);

				break;
			case "catid_prodid_slug":

				$utm_content_res = sprintf( 'cat%s--prod%s-%s',
					$category_id,
					$product->get_id(),
					$product->get_slug()
				);

				break;
			case "product_slug":

				$utm_content_res = $product->get_slug();

				break;
			default:
				$utm_content_res = $product->get_id();
		}

		if ( $use_utm === 'enabled' ) {
			$tag_value = sprintf(
				'%s?utm_source=%s&amp;utm_medium=%s&amp;utm_campaign=%s&amp;utm_content=%s&amp;utm_term=%s',
				$tag_value,
				stripslashes( htmlspecialchars( $utm_source ) ),
				stripslashes( htmlspecialchars( $utm_medium ) ),
				stripslashes( htmlspecialchars( $utm_campaign ) ),
				$utm_content_res,
				$utm_term
			);
			if ( $roistat !== '' ) {
				$tag_value = $tag_value . '&amp;rs=' . $roistat;
			}
		}
		return $tag_value;

	}

	/**
	 * Change variable url.
	 * 
	 * Function for `y4ym_f_variable_url` filter-hook.
	 * 
	 * @param string $tag_value
	 * @param array $data_arr keys: `product`, `offer`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function change_variable_url( $tag_value, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $tag_value, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $tag_value;
		}

		$product = $data_arr['product'];
		$offer = $data_arr['offer'];
		$category_id = $data_arr['feed_category_id'];

		$use_utm = common_option_get(
			'y4ymp_use_utm',
			'disabled',
			$feed_id,
			'y4ym'
		);
		// utm_source
		$utm_source = common_option_get(
			'y4ymp_utm_source',
			'market.yandex.ru',
			$feed_id,
			'y4ym'
		);
		// utm_medium
		$utm_medium = common_option_get(
			'y4ymp_utm_medium',
			'cpc',
			$feed_id,
			'y4ym'
		);
		$utm_medium = str_replace( '{catid}', $category_id, $utm_medium );
		$utm_medium = str_replace( '{productid}', $product->get_id(), $utm_medium );
		// utm_campaign
		$utm_campaign = common_option_get(
			'y4ymp_utm_campaign',
			'',
			$feed_id,
			'y4ym'
		);
		$utm_campaign = str_replace( '{catid}', $category_id, $utm_campaign );
		$utm_campaign = str_replace( '{productid}', $product->get_id(), $utm_campaign );
		// utm_content
		$utm_content = common_option_get(
			'y4ymp_utm_content',
			'catid_prodid_slug',
			$feed_id,
			'y4ym'
		);
		// utm_term
		$utm_term = common_option_get(
			'y4ymp_utm_term',
			'',
			$feed_id,
			'y4ym'
		);
		if ( $utm_term !== '' ) {
			$utm_term = str_replace( '{catid}', $category_id, $utm_term );
			$utm_term = str_replace( '{productid}', $product->get_id(), $utm_term );
			$utm_term = str_replace( '{productorvarid}', $offer->get_id(), $utm_term );
			$utm_term = str_replace( '{productslug}', $product->get_slug(), $utm_term );
		}
		// rs
		$roistat = common_option_get(
			'y4ymp_roistat',
			'',
			$feed_id,
			'y4ym'
		);
		if ( $roistat !== '' ) {
			$roistat = str_replace( '{catid}', $category_id, $roistat );
			$roistat = str_replace( '{productid}', $product->get_id(), $roistat );
			$roistat = str_replace( '{productorvarid}', $offer->get_id(), $roistat );
		}

		switch ( $utm_content ) {
			case "catid":

				$utm_content_res = $category_id;

				break;
			case "catid_prodid":

				$utm_content_res = sprintf( 'cat%s--prod%s',
					$category_id,
					$product->get_id()
				);

				break;
			case "catid_prodid_slug":

				$utm_content_res = sprintf( 'cat%s--prod%s-%s',
					$category_id,
					$product->get_id(),
					$product->get_slug()
				);

				break;
			case "product_slug":

				$utm_content_res = $product->get_slug();

				break;
			default:
				$utm_content_res = $product->get_id();
		}

		if ( $use_utm === 'enabled' ) {
			if ( false == strpos( $tag_value, '?' ) ) {
				$tag_value = sprintf(
					'%s?utm_source=%s&amp;utm_medium=%s&amp;utm_campaign=%s&amp;utm_content=%s&amp;utm_term=%s',
					$tag_value,
					stripslashes( htmlspecialchars( $utm_source ) ),
					stripslashes( htmlspecialchars( $utm_medium ) ),
					stripslashes( htmlspecialchars( $utm_campaign ) ),
					$utm_content_res,
					$utm_term
				);
				if ( $roistat !== '' ) {
					$tag_value = $tag_value . '&amp;rs=' . $roistat;
				}
			} else {
				$tag_value = sprintf(
					'%s&amp;utm_source=%s&amp;utm_medium=%s&amp;utm_campaign=%s&amp;utm_content=%s&amp;utm_term=%s',
					$tag_value,
					stripslashes( htmlspecialchars( $utm_source ) ),
					stripslashes( htmlspecialchars( $utm_medium ) ),
					stripslashes( htmlspecialchars( $utm_campaign ) ),
					$utm_content_res,
					$utm_term
				);
				if ( $roistat !== '' ) {
					$tag_value .= '&amp;rs=' . $roistat;
				}
			}
		}

		return $tag_value;

	}

	/**
	 * Changing the category ID for simple products.
	 * 
	 * Function for `y4ym_f_simple_tag_value_categoryid` filter-hooks.
	 * 
	 * @param string $tag_value
	 * @param array $data_arr keys: `product`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function simple_tag_value_categoryid( $tag_value, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $tag_value, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $tag_value;
		}

		$tags_as_cat = common_option_get(
			'y4ymp_tags_as_cat',
			'disabled',
			$feed_id,
			'y4ym'
		);

		if ( $tags_as_cat === 'on_cat' ) {
			// если категорий у товара нет пробуем подставить метку
			if ( $tag_value == '' ) {
				// если категорий нет, но есть метки
				$product_tags = get_the_terms( $data_arr['product']->get_id(), 'product_tag' );
				if ( is_array( $product_tags ) ) {
					foreach ( $product_tags as $termin ) {
						$tag_value = $termin->term_id;
						break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
					}
				}
			}
		} else if ( $tags_as_cat === 'on' ) { // теги приоритетнее категории
			$product_tags = get_the_terms( $data_arr['product']->get_id(), 'product_tag' );
			if ( is_array( $product_tags ) ) {
				foreach ( $product_tags as $termin ) {
					$tag_value = $termin->term_id;
					break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
				}
			}
		}

		return $tag_value;

	}

	/**
	 * Changing the category ID for variable products.
	 * 
	 * Function for `y4ym_f_svariable_tag_value_categoryid` filter-hooks.
	 * 
	 * @param string $tag_value
	 * @param array $data_arr keys: `product`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function variable_tag_value_categoryid( $tag_value, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $tag_value, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $tag_value;
		}

		$tags_as_cat = common_option_get(
			'y4ymp_tags_as_cat',
			'disabled',
			$feed_id,
			'y4ym'
		);

		if ( $tags_as_cat === 'on_cat' ) {
			// если категорий у товара нет пробуем подставить метку
			if ( $tag_value == '' ) {
				// если категорий нет, но есть метки
				$product_tags = get_the_terms( $data_arr['product']->get_id(), 'product_tag' );
				if ( is_array( $product_tags ) ) {
					foreach ( $product_tags as $termin ) {
						$tag_value = $termin->term_id;
						break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
					}
				}
			}
		} else if ( $tags_as_cat === 'on' ) { // теги приоритетнее категории
			$product_tags = get_the_terms( $data_arr['product']->get_id(), 'product_tag' );
			if ( is_array( $product_tags ) ) {
				foreach ( $product_tags as $termin ) {
					$tag_value = $termin->term_id;
					break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
				}
			}
		}

		return $tag_value;

	}

	/**
	 * Changing the quantity of products in stock for simple products.
	 * 
	 * Function for `y4ym_f_simple_tag_value_count`, `y4ym_f_simple_tag_value_amount`,
	 * `y4ym_f_simple_tag_value_qty` filter-hooks.
	 * 
	 * @param string $tag_value
	 * @param array $data_arr keys: `product`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function simple_tag_value_count( $tag_value, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $tag_value, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $tag_value;
		}

		// Если активен плагин WooCommerce Multi Inventory & Warehouses
		if ( class_exists( 'WooCommerce_Multi_Inventory' ) ) {
			$inventories = common_option_get(
				'y4ymp_inventories',
				'disabled',
				$feed_id,
				'y4ym'
			);
			if ( ! empty( $inventories ) && $inventories !== 'disabled' ) {
				$inventories_stock_meta = get_post_meta(
					$data_arr['product']->get_id(),
					'woocommerce_multi_inventory_inventories_stock',
					true
				);
				if ( is_array( $inventories_stock_meta ) ) {
					if ( isset( $inventories_stock_meta[ $inventories ] ) ) {
						$tag_value = $inventories_stock_meta[ $inventories ];
					}
				}
			}
		}

		return $tag_value;

	}

	/**
	 * Changing the quantity of products in stock for variable products.
	 * 
	 * Function for `y4ym_f_variable_tag_value_count`, `y4ym_f_variable_tag_value_amount`,
	 * `y4ym_f_variable_tag_value_qty` filter-hooks.
	 * 
	 * @param string $tag_value
	 * @param array $data_arr keys: `product`, `offer`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function variable_tag_value_count( $tag_value, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $tag_value, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $tag_value;
		}

		// Если активен плагин WooCommerce Multi Inventory & Warehouses
		if ( class_exists( 'WooCommerce_Multi_Inventory' ) ) {
			$inventories = common_option_get(
				'y4ymp_inventories',
				'disabled',
				$feed_id,
				'y4ym'
			);
			if ( ! empty( $inventories ) && $inventories !== 'disabled' ) {
				$inventories_stock_meta = get_post_meta(
					$data_arr['offer']->get_id(),
					'woocommerce_multi_inventory_inventories_stock',
					true
				);
				if ( is_array( $inventories_stock_meta ) ) {
					if ( isset( $inventories_stock_meta[ $inventories ] ) ) {
						$tag_value = $inventories_stock_meta[ $inventories ];
					}
				}
			}
		}

		return $tag_value;

	}

	/**
	 * Changes the name of simple products.
	 * 
	 * @param string $result_xml_name
	 * @param array $data_arr keys: `product`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function change_simple_name( $result_xml_name, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $result_xml_name, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $result_xml_name;
		}

		$product = $data_arr['product'];

		$add_for_name_to_beginning_arr = [];
		$add_for_name_to_end_arr = [];
		$add_for_name_to_beginning_simple_arr = [];
		$add_for_name_to_end_simple_arr = [];

		if ( $product->is_type( 'simple' ) ) {
			// в конец простых товаров
			for ( $i = 0; $i < 3; $i++ ) {
				$add_for_name_to_beginning_simple_arr = $this->add_to_product_name_simple(
					$add_for_name_to_beginning_simple_arr,
					'y4ymp_simple_name_var' . $i,
					$data_arr,
					$feed_id
				);
			}

			for ( $i = 1; $i < 4; $i++ ) {
				// в начало всех товаров
				$add_for_name_to_beginning_arr = $this->add_to_product_name_simple(
					$add_for_name_to_beginning_arr,
					'y4ymp_add_name_beginning_all_' . $i,
					$data_arr,
					$feed_id
				);

				// в конец всех товаров
				$add_for_name_to_end_arr = $this->add_to_product_name_simple(
					$add_for_name_to_end_arr,
					'y4ymp_add_name_end_all_' . $i,
					$data_arr,
					$feed_id
				);
			}
		}

		$result_xml_name = trim( sprintf( '%s %s %s %s %s',
			implode( ', ', $add_for_name_to_beginning_arr ),
			implode( ', ', $add_for_name_to_beginning_simple_arr ),
			$result_xml_name, implode( ', ', $add_for_name_to_end_simple_arr ),
			implode( ', ', $add_for_name_to_end_arr )
		) );
		$result_xml_name = apply_filters(
			'y4ymp_f_yml_change_name',
			$result_xml_name,
			$data_arr,
			[
				'beginning_arr' => $add_for_name_to_beginning_arr,
				'end_arr' => $add_for_name_to_end_arr,
				'beginning_simple_arr' => $add_for_name_to_beginning_simple_arr,
				'end_simple_arr' => $add_for_name_to_end_simple_arr
			],
			$feed_id
		);
		return $result_xml_name;

	}

	/**
	 * Changes the name of variable products.
	 * 
	 * @param string $result_xml_name
	 * @param array $data_arr keys: `product`, `offer`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function change_variable_name( $result_xml_name, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $result_xml_name, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $result_xml_name;
		}

		$product = $data_arr['product'];
		$result_xml_name = $product->get_name(); // нужна чтобы при уникализации заголовков "не двоило" заголовок

		$add_for_name_to_beginning_arr = [];
		$add_for_name_to_end_arr = [];

		for ( $i = 1; $i < 4; $i++ ) {
			// в началао всех товаров
			$add_for_name_to_beginning_arr = $this->add_to_product_name_variable(
				$add_for_name_to_beginning_arr,
				'y4ymp_add_name_beginning_all_' . $i,
				$data_arr,
				$feed_id
			);

			// в конец всех товаров
			$add_for_name_to_end_arr = $this->add_to_product_name_variable(
				$add_for_name_to_end_arr,
				'y4ymp_add_name_end_all_' . $i,
				$data_arr,
				$feed_id
			);
		}

		$result_xml_name = trim( sprintf( '%s %s %s',
			implode( ', ', $add_for_name_to_beginning_arr ),
			$result_xml_name,
			implode( ', ', $add_for_name_to_end_arr )
		) );
		$result_xml_name = apply_filters(
			'y4ymp_f_variable_yml_change_name',
			$result_xml_name,
			$data_arr,
			[
				'beginning_arr' => $add_for_name_to_beginning_arr,
				'end_arr' => $add_for_name_to_end_arr
			],
			$feed_id
		);

		return $result_xml_name;

	}

	/**
	 * Add to produc name simple.
	 * 
	 * @param array $product_name_arr
	 * @param string $opt_name
	 * @param array $data_arr
	 * @param string $feed_id
	 * 
	 * @return array
	 */
	private function add_to_product_name_simple( $product_name_arr, $opt_name, $data_arr, $feed_id ) {

		$product = $data_arr['product'];
		$opt_val = common_option_get(
			$opt_name,
			'disabled',
			$feed_id,
			'y4ym'
		);
		if ( ! empty( $opt_val ) ) {
			switch ( $opt_val ) {
				case "disabled":
					// выгружать штрихкод нет нужды
					break;
				case "sku":
					// выгружать из артикула
					$sku_yml = $product->get_sku();
					if ( ! empty( $sku_yml ) ) {
						$product_name_arr[] = $sku_yml;
					}
					break;
				default:
					$opt_val = (int) $opt_val;
					$opt_val_yml = $product->get_attribute( wc_attribute_taxonomy_name_by_id( $opt_val ) );
					if ( ! empty( $opt_val_yml ) ) {
						$product_name_arr[] = ucfirst( urldecode( $opt_val_yml ) );
					}
			}
		}
		return $product_name_arr;

	}

	/**
	 * Add to produc name variable.
	 * 
	 * @param array $product_name_arr
	 * @param string $opt_name
	 * @param array $data_arr
	 * @param string $feed_id
	 * 
	 * @return array
	 */
	private function add_to_product_name_variable( $product_name_arr, $opt_name, $data_arr, $feed_id ) {

		$product = $data_arr['product'];
		$offer = $data_arr['offer'];
		$opt_val = common_option_get(
			$opt_name,
			'disabled',
			$feed_id,
			'y4ym'
		);

		if ( ! empty( $opt_val ) ) {
			switch ( $opt_val ) { // disabled, sku, или {attr_id} 
				case "disabled":
					// выгружать штрихкод нет нужды
					break;
				case "sku":
					// выгружать из артикула
					$sku_yml = $offer->get_sku(); // артикул
					if ( ! empty( $sku_yml ) ) {
						$product_name_arr[] = $sku_yml;
					} else {
						// своего артикула у вариации нет. Пробуем подставить общий sku
						$sku_yml = $product->get_sku();
						if ( ! empty( $sku_yml ) ) {
							$product_name_arr[] = $sku_yml;
						}
					}
					break;
				default:
					$opt_val = (int) $opt_val;
					$opt_val_yml = $offer->get_attribute( wc_attribute_taxonomy_name_by_id( $opt_val ) );
					if ( ! empty( $opt_val_yml ) ) {
						$product_name_arr[] = ucfirst( urldecode( $opt_val_yml ) );
					} else {
						$opt_val_yml = $product->get_attribute( wc_attribute_taxonomy_name_by_id( $opt_val ) );
						if ( ! empty( $opt_val_yml ) ) {
							$product_name_arr[] = ucfirst( urldecode( $opt_val_yml ) );
						}
					}
			}
		}

		return $product_name_arr;

	}

	/**
	 * Changing the simple product product description.
	 * 
	 * Function for `y4ym_f_simple_tag_value_description` filter-hook.
	 * 
	 * @param string $tag_value
	 * @param array $data_arr keys: `product`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function change_simple_description( $tag_value, $data_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $tag_value, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $tag_value;
		}

		$use_del_vc = common_option_get(
			'y4ymp_use_del_vc',
			'disabled',
			$feed_id,
			'y4ym'
		);
		if ( $use_del_vc === 'enabled' ) {
			$tag_value = $this->del_visual_composer_tags( $tag_value );
		}
		$del_tags_atr = common_option_get(
			'y4ymp_del_tags_atr',
			'disabled',
			$feed_id,
			'y4ym'
		);
		if ( $del_tags_atr === 'enabled' ) {
			$tag_value = preg_replace( "#(</?\w+)(?:\s(?:[^<>/]|/[^<>])*)?(/?>)#ui", '$1$2', $tag_value );
		}
		return $tag_value;

	}

	/**
	 * Changing the variable product product description.
	 * 
	 * Function for `y4ym_f_variable_tag_value_description` filter-hook.
	 * 
	 * @param string $tag_value
	 * @param array $data_arr keys: `product`, `offer`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function change_variable_description( $tag_value, $post_id, $product, $feed_id = '1' ) {

		$sanitize_result = sanitize_variable_from_yml( $tag_value, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $tag_value;
		}

		$use_del_vc = common_option_get(
			'y4ymp_use_del_vc',
			'disabled',
			$feed_id,
			'y4ym'
		);
		if ( $use_del_vc === 'enabled' ) {
			$tag_value = $this->del_visual_composer_tags( $tag_value );
		}
		$del_tags_atr = common_option_get(
			'y4ymp_del_tags_atr',
			'disabled',
			$feed_id,
			'y4ym'
		);
		if ( $del_tags_atr === 'enabled' ) {
			$tag_value = preg_replace( "#(</?\w+)(?:\s(?:[^<>/]|/[^<>])*)?(/?>)#ui", '$1$2', $tag_value );
		}
		return $tag_value;

	}

	/**
	 * Удаляет шорткоды Visual Composer.
	 * 
	 * @param string $text
	 * 
	 * @return string
	 */
	private function del_visual_composer_tags( $text ) {

		$tags_for_del_arr = [
			'\[vc_row]',
			'\[vc_column]',
			'\[vc_column_text]',
			'\[\/vc_row]',
			'\[\/vc_column]',
			'\[\/vc_column_text]',
			'\[\/row]',
			'\[\/col]',
			'\[\/vc_accordion_tab]',
			'\[\/vc_accordion]',
			'\[\/vc_column_inner]',
			'\[\/vc_row_inner]',
			'\[\/vc_separator]',
			'\[\/vc_images_carousel]'
		];
		$tags_for_del_arr = apply_filters( 'y4ymp_f_visual_composer_tags_for_del', $tags_for_del_arr );
		$text = preg_replace( '/\[col.*?]/is', '', $text );
		$text = preg_replace( '/\[row.*?]/is', '', $text );
		$text = preg_replace( '/\[ux_video.*?]/is', '', $text );
		$text = preg_replace( '/\[vc_video link.*?]/is', '', $text );
		$text = preg_replace( '/\[vc_raw_html].*?\[\/vc_raw_html]/is', '', $text );
		$text = preg_replace( '/\[vc_row.*?]/is', '', $text );
		$text = preg_replace( '/\[vc_column.*?]/is', '', $text );
		$text = preg_replace( '/\[vc_single_image.*?]/is', '', $text );
		$text = preg_replace( '/\[vc_accordion_tab.*?]/is', '', $text );
		$text = preg_replace( '/\[vc_accordion.*?]/is', '', $text );
		$text = preg_replace( '/\[vc_text_separator title="(.*?)".*?]/is', '<p>\\1</p>', $text );
		$text = preg_replace( '/\[vc_text_separator title=”(.*?)”.*?]/is', '<p>\\1</p>', $text );
		$text = preg_replace( '/\[vc_column_inner.*?]/is', '', $text );
		$text = preg_replace( '/\[vc_row_inner.*?]/is', '', $text );
		$text = preg_replace( '/\[vc_separator.*?]/is', '', $text );
		$text = preg_replace( '/\[vc_images_carousel.*?]/is', '', $text );
		foreach ( $tags_for_del_arr as $value ) {
			$delteg = sprintf( '/%s/', $value );
			$text = preg_replace( $delteg, '', $text );
		}
		return $text;

	}

	/**
	 * Append tags to simple offer.
	 * 
	 * Function for `y4ym_f_append_simple_offer` action-hook.
	 * 
	 * @param string $result_xml
	 * @param array $args_arr keys: `product`, `feed_category_id`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function append_simple_offer( $result_xml, $args_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $result_xml, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $result_xml;
		}

		// TODO: переработать блок с конструктором параметров
		$n = '1';
		if ( ! defined( 'Y4YMP_PARAM_N' ) ) {
			define( 'Y4YMP_PARAM_N', 15 );
		}

		$opt_name = sprintf( 'y4ymp_constructor_params%s', $feed_id );
		if ( is_multisite() ) {
			$constructor_params_arr = get_blog_option( get_current_blog_id(), $opt_name, [] );
		} else {
			$constructor_params_arr = get_option( $opt_name, [] );
		}
		// аrray(14) { 
		// 	// <param name="X" unit="Y">Z</param>
		// 	[0]=> array(8) { 
		//		// Use
		// 		["param_use1"]=> string(7) "enabled" 
		//		// Name (X)
		// 		["param_name_s1"]=> string(23) "Обхват талии" 
		// 		["param_name_custom1"]=> string(1) "2" 
		//		// Unit (Y)
		// 		["param_unit_s1"]=> string(2) "74" 
		// 		["param_unit_default_s1"]=> string(5) "Japan" 
		// 		["param_unit_custom1"]=> string(1) "3" 
		//		// Value (Z)
		// 		["param_value_s1"]=> string(2) "74" 
		// 		["param_value_custom1"]=> string(1) "8" 
		// 	} 
		//  [1]=> array(8) { 
		// 		["param_use2"]=> string(7) "enabled" 
		//  	... 
		// }
		if ( ! empty( $constructor_params_arr ) && isset( $constructor_params_arr[ $feed_id ] ) ) {
			for ( $i = 1; $i < Y4YMP_PARAM_N; $i++ ) {

				if ( $constructor_params_arr[ $i ]['param_use'] === 'enabled' ) {
					$tag_value = '';
					$attr_param_arr = []; // атрибуты параметра
					// name
					if ( empty( $constructor_params_arr[ $i ]['param_name_custom'] ) ) {
						$attr_param_arr['name'] = $constructor_params_arr[ $i ]['param_name_select'];
					} else {
						$attr_param_arr['name'] = $constructor_params_arr[ $i ]['param_name_custom'];
					}

					// unit
					if ( $constructor_params_arr[ $i ]['param_unit_select'] === 'disabled' ) {
						// без unit
					} else {
						if ( empty( $constructor_params_arr[ $i ]['param_unit_custom'] ) ) {
							$unit_attr_id = (int) $constructor_params_arr[ $i ]['param_unit_select'];
							$unit_value = $args_arr['product']->get_attribute( wc_attribute_taxonomy_name_by_id( $unit_attr_id ) );
							if ( empty( $unit_value ) ) {
								$attr_param_arr['unit'] = $constructor_params_arr[ $i ]['param_unit_default_select'];
							} else {
								$attr_param_arr['unit'] = $unit_value;
							}
						} else {
							// значение unit по умолчанию
							$attr_param_arr['unit'] = $constructor_params_arr[ $i ]['param_unit_custom'];
						}
					}

					// value
					if ( $constructor_params_arr[ $i ]['param_value_select'] === 'disabled' ) {
						// без значения
						// ? возможно тут стоит предусмотреть param без закрывающего тега
					} else {
						if ( empty( $constructor_params_arr[ $i ]['param_value_custom'] ) ) {
							$value_id = (int) $constructor_params_arr[ $i ]['param_value_select'];
							$tag_value = $args_arr['product']->get_attribute( wc_attribute_taxonomy_name_by_id( $value_id ) );
						} else {
							$tag_value = $constructor_params_arr[ $i ]['param_value_custom'];
						}
					}

					if ( ! empty( $attr_param_arr['name'] ) ) {
						$result_xml .= new Y4YM_Get_Paired_Tag(
							'param',
							$tag_value,
							$attr_param_arr
						);
					}
				}
			}
		}
		return $result_xml;

	}

	/**
	 * Append tags to variable offer.
	 * 
	 * Function for `y4ym_f_append_variable_offer` action-hook.
	 * 
	 * @param string $result_xml
	 * @param array $args_arr keys: `product`, `offer`, `feed_category_id`.
	 * @param string $feed_id
	 * 
	 * @return string
	 */
	public function append_variable_offer( $result_xml, $args_arr, $feed_id ) {

		$sanitize_result = sanitize_variable_from_yml( $result_xml, 'y4ymp' );
		if ( false == $sanitize_result ) {
			return $result_xml;
		}

		// TODO: переработать блок с конструктором параметров
		$n = '1';
		if ( ! defined( 'Y4YMP_PARAM_N' ) ) {
			define( 'Y4YMP_PARAM_N', 15 );
		}

		$opt_name = sprintf( 'y4ymp_constructor_params%s', $feed_id );
		if ( is_multisite() ) {
			$constructor_params_arr = get_blog_option( get_current_blog_id(), $opt_name, [] );
		} else {
			$constructor_params_arr = get_option( $opt_name, [] );
		}
		// аrray(14) { 
		// 	// <param name="X" unit="Y">Z</param>
		// 	[0]=> array(8) { 
		//		// Use
		// 		["param_use1"]=> string(7) "enabled" 
		//		// Name (X)
		// 		["param_name_s1"]=> string(23) "Обхват талии" 
		// 		["param_name_custom1"]=> string(1) "2" 
		//		// Unit (Y)
		// 		["param_unit_s1"]=> string(2) "74" 
		// 		["param_unit_default_s1"]=> string(5) "Japan" 
		// 		["param_unit_custom1"]=> string(1) "3" 
		//		// Value (Z)
		// 		["param_value_s1"]=> string(2) "74" 
		// 		["param_value_custom1"]=> string(1) "8" 
		// 	} 
		//  [1]=> array(8) { 
		// 		["param_use2"]=> string(7) "enabled" 
		//  	... 
		// }
		if ( ! empty( $constructor_params_arr ) && isset( $constructor_params_arr[ $feed_id ] ) ) {
			for ( $i = 1; $i < Y4YMP_PARAM_N; $i++ ) {

				if ( $constructor_params_arr[ $i ]['param_use'] === 'enabled' ) {
					$tag_value = '';
					$attr_param_arr = []; // атрибуты параметра
					// name
					if ( empty( $constructor_params_arr[ $i ]['param_name_custom'] ) ) {
						$attr_param_arr['name'] = $constructor_params_arr[ $i ]['param_name_select'];
					} else {
						$attr_param_arr['name'] = $constructor_params_arr[ $i ]['param_name_custom'];
					}

					// unit
					if ( $constructor_params_arr[ $i ]['param_unit_select'] === 'disabled' ) {
						// без unit
					} else {
						if ( empty( $constructor_params_arr[ $i ]['param_unit_custom'] ) ) {
							$unit_attr_id = (int) $constructor_params_arr[ $i ]['param_unit_select'];
							$unit_value = $args_arr['offer']->get_attribute( wc_attribute_taxonomy_name_by_id( $unit_attr_id ) );
							if ( empty( $unit_value ) ) {
								$unit_value = $args_arr['product']->get_attribute( wc_attribute_taxonomy_name_by_id( $unit_attr_id ) );
								if ( empty( $unit_value ) ) {
									// значение unit по умолчанию
									$attr_param_arr['unit'] = $constructor_params_arr[ $i ]['param_unit_custom'];
								} else {
									$attr_param_arr['unit'] = $unit_value;
								}
							} else {
								$attr_param_arr['unit'] = $unit_value;
							}
						} else {
							// значение unit по умолчанию
							$attr_param_arr['unit'] = $constructor_params_arr[ $i ]['param_unit_custom'];
						}
					}

					// value
					if ( $constructor_params_arr[ $i ]['param_value_select'] === 'disabled' ) {
						// без значения
						// ? возможно тут стоит предусмотреть param без закрывающего тега
					} else {
						if ( empty( $constructor_params_arr[ $i ]['param_value_custom'] ) ) {
							$value_id = (int) $constructor_params_arr[ $i ]['param_value_select'];
							$tag_value = $args_arr['offer']->get_attribute( wc_attribute_taxonomy_name_by_id( $value_id ) );
							if ( empty( $tag_value ) ) {
								$tag_value = $args_arr['product']->get_attribute( wc_attribute_taxonomy_name_by_id( $value_id ) );
							}
						} else {
							$tag_value = $constructor_params_arr[ $i ]['param_value_custom'];
						}
					}

					if ( ! empty( $attr_param_arr['name'] ) ) {
						$result_xml .= new Y4YM_Get_Paired_Tag(
							'param',
							$tag_value,
							$attr_param_arr
						);
					}
				}
			}
		}
		return $result_xml;

	}

} // end class Y4YMP_Generation_Hoocked