<?php

/**
 * The class hoocked YML for Yandex Market interface.
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 * @version    6.0.15 (28-11-2025)
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/admin
 */

/**
 * The class hoocked YML for Yandex Market interface.
 *
 * Depends on: 
 *   - classes: 
 *   - functions: `common_option_get`, `common_option_upd`
 *   - constants: `Y4YMP_PLUGIN_DIR_PATH`.
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/admin
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
final class Y4YMP_Interface_Hoocked {

	/**
	 * The class hoocked YML for Yandex Market interface.
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

		add_filter( 'y4ym_f_tabs_arr', [ $this, 'tabs_arr' ], 10, 2 );
		add_action( 'y4ym_f_html_template_tab', [ $this, 'html_template_tab' ], 10, 2 );

		/* Мета-поля для категорий товаров */
		add_action( "product_cat_edit_form_fields", [ $this, 'add_meta_product_cat' ], 10, 2 );
		add_action( 'edited_product_cat', [ $this, 'save_meta_product_cat' ], 10, 1 );
		add_action( 'create_product_cat', [ $this, 'save_meta_product_cat' ], 10, 1 );

		// выпадающее меню массовых действий с товарами
		add_filter( 'bulk_actions-' . 'edit-product', [ $this, 'register_y4ymp_bulk_actions' ], 10, 1 );
		add_filter( 'handle_bulk_actions-' . 'edit-product', [ $this, 'bulk_action_handler' ], 10, 3 );

		// выводим различные оповещения и сохраняем настройки про-версии
		add_action( 'admin_init', [ $this, 'notice_and_save_settings_pro' ], 10, 1 );

		add_filter( 'y4ym_f_post_meta_arr', [ $this, 'add_post_meta_arr' ], 39, 1 );
		add_action( 'y4ym_prepend_individual_settings_tab', [ $this, 'add_settings_individual_settings_tab' ], 39, 1 );
		// https://wpruse.ru/woocommerce/custom-fields-in-products/
		// https://wpruse.ru/woocommerce/custom-fields-in-variations/
		add_action( 'save_post', [ $this, 'save_product' ], 31, 3 );

		// создаем новую колонку
		add_filter( 'manage_edit-' . 'product' . '_columns', [ $this, 'add_views_column' ], 10, 1 );
		// заполняем колонку данными
		add_action( 'manage_product_posts_custom_column', [ $this, 'fill_views_column' ], 10, 2 );

		add_action( 'y4ym_f_feedback_additional_info', [ $this, 'feedback_additional_info' ], 10, 1 );

	}

	/**
	 * Adds a new tab to the plugin settings page.
	 * 
	 * @param array $tabs_arr
	 * @param array $data_arr
	 * 
	 * @return array
	 */
	public function tabs_arr( $tabs_arr, $data_arr ) {

		$tabs_arr['constructor_params_tab'] = sprintf(
			'%s',
			__( 'Constructor params', 'yml-for-yandex-market-pro' )
		);
		return $tabs_arr;

	}

	/**
	 * Creates a new tab.
	 * 
	 * @param string $html_template
	 * @param array $data_arr
	 * 
	 * @return string
	 */
	public function html_template_tab( $html_template, $data_arr ) {

		if ( $data_arr['tab_name'] === 'constructor_params_tab' ) {
			$html_template = __DIR__ . '/partials/settings-page/views/html-admin-settings-feed-tab-constructor-params.php';
		}
		return $html_template;

	}

	/** // TODO: Потенциально откзаться от дерева категорий для Яндекс Маркет.
	 * TODO: удалить умножение цены (28-11-2025)
	 * Function for `(taxonomy)_edit_form_fields` action-hook.
	 * 
	 * @see https://www.php.net/manual/ru/class.simplexmlelement.php
	 * 
	 * @param WP_Term $tag      Current taxonomy term object.
	 * @param string  $taxonomy Current taxonomy slug.
	 *
	 * @return void
	 */
	public static function add_meta_product_cat( $term, $taxonomy ) {

		?>
		<tr class="form-field term-parent-wrap">
			<th scope="row" valign="top">
				<label>
					<?php esc_html_e( "Don't use these settings for feeds", "yml-for-yandex-market-pro" ); ?>
				</label>
			</th>
			<td>
				<select style="width: 100%;" id="yfymp_disabled_for_feeds" name="yfymp_disabled_for_feeds[]" size="6" multiple>
					<?php
					if ( is_multisite() ) {
						$cur_blog_id = get_current_blog_id();
					} else {
						$cur_blog_id = '0';
					}
					$disabled_for_feeds_arr = get_term_meta( $term->term_id, 'yfymp_disabled_for_feeds', true );
					if ( ! is_array( $disabled_for_feeds_arr ) ) {
						$disabled_for_feeds_arr = [];
					}
					$y4ym_settings_arr = univ_option_get( 'y4ym_settings_arr' );
					$y4ym_settings_arr_keys_arr = array_keys( $y4ym_settings_arr );
					for ( $i = 0; $i < count( $y4ym_settings_arr_keys_arr ); $i++ ) {
						$feed_id = (string) $y4ym_settings_arr_keys_arr[ $i ];
						if ( $y4ym_settings_arr[ $feed_id ]['y4ym_feed_assignment'] === '' ) {
							$feed_assignment = '';
						} else {
							$feed_assignment = sprintf( ' (%s)',
								$y4ym_settings_arr[ $feed_id ]['y4ym_feed_assignment']
							);
						}
						if ( in_array( $feed_id, $disabled_for_feeds_arr ) ) {
							$selected = 'selected';
						} else {
							$selected = '';
						}
						printf( '<option value="%s" %s>%s %s: feed-xml-%s.xml%s</option>',
							esc_attr( $feed_id ),
							esc_attr( $selected ),
							esc_html__( 'Feed', 'yml-for-yandex-market' ),
							esc_html( $feed_id ),
							esc_html( $cur_blog_id ),
							esc_html( $feed_assignment )
						);
					}
					?>
				</select>
			</td>
		</tr>
		<tr class="form-field term-parent-wrap">
			<th scope="row" valign="top">
				<label>
					<?php esc_html_e( 'Multiply the price', 'yml-for-yandex-market-pro' ); ?>
				</label>
			</th>
			<td>
				<?php $multiply_price_value = esc_attr( get_term_meta( $term->term_id, 'yfymp_multiply_price_value', true ) ); ?>
				<input type="number" step="any" name="yfymp_cat_meta[yfymp_multiply_price_value]"
					value="<?php echo $multiply_price_value; ?>" />
				<p class="description">
					<?php
					printf( '<strong>%s!</strong> %s. %s.<br/>%s)',
						esc_html__( 'Warning', 'yml-for-yandex-market-pro' ),
						esc_html__(
							'This option is considered obsolete and will be removed in future versions of the plugin',
							'yml-for-yandex-market-pro'
						),
						esc_html__(
							'Instead, it is recommended to use the option below', 'yml-for-yandex-market-pro'
						),
						esc_html__(
							'The price of the product will be multiplied by the value from this field. Specify 1 so that the price does not change',
							'yml-for-yandex-market-pro'
						)
					);
					?>
				</p>
			</td>
		</tr>
		<tr class="form-field term-parent-wrap">
			<th scope="row" valign="top">
				<label>
					<?php printf( '%s (%s %% %s)',
						esc_html__( 'Change the product price', 'yml-for-yandex-market-pro' ),
						esc_html__( 'add or subtract', 'yml-for-yandex-market-pro' ),
						esc_html__( 'of the original cost of the product', 'yml-for-yandex-market-pro' )
					); ?>
				</label>
			</th>
			<td>
				<?php $price_percentage = esc_attr( get_term_meta( $term->term_id, 'yfymp_price_percentage', true ) ); ?>
				<input type="number" step="0.5" name="yfymp_cat_meta[yfymp_price_percentage]"
					value="<?php echo $price_percentage; ?>" />
			</td>
		</tr>
		<tr class="form-field term-parent-wrap">
			<th scope="row" valign="top">
				<label>
					<?php esc_html_e( 'Add to price', 'yml-for-yandex-market-pro' ); ?><br />(
					<?php esc_html_e( 'negative values can be used', 'yml-for-yandex-market-pro' ); ?>)
				</label>
			</th>
			<td>
				<?php $add_to_price_value = esc_attr( get_term_meta( $term->term_id, 'yfymp_add_to_price_value', true ) ); ?>
				<input type="number" step="any" name="yfymp_cat_meta[yfymp_add_to_price_value]"
					value="<?php echo $add_to_price_value; ?>" />
				<p class="description">
					<?php esc_html_e(
						'This value will be added to the value of the product. Specify 0 so that the price does not change',
						'yml-for-yandex-market-pro'
					); ?>
				</p>
			</td>
		</tr>
		<?php
		$terms = get_terms( [ 'taxonomy' => 'ymarket', 'hide_empty' => 0, 'parent' => 0 ] );
		if ( $terms && ! is_wp_error( $terms ) ) :
			?>
			<tr class="form-field term-parent-wrap">
				<th scope="row" valign="top"><label>
						<?php esc_html_e( 'Category for Yandex Market', 'yml-for-yandex-market-pro' ); ?>
					</label></th>
				<td>
					<?php $params_arr[] = esc_attr( get_term_meta( $term->term_id, 'yfymp_ymarket', true ) ); ?>
					<select style="width: 100%;" id="yfymp_ymarket" name="yfymp_cat_meta[yfymp_ymarket]">
						<option value="disabled" <?php selected( esc_attr( get_term_meta( $term->term_id, 'yfymp_ymarket', true ) ), 'disabled' ); ?>>
							<?php esc_html_e( 'Disabled', 'yfym' ); ?>
						</option>
						<?php
						foreach ( $terms as $term ) {
							echo the_cat_tree( $term->taxonomy, $term->term_id, $params_arr );
						} ?>
					</select>
					<p class="description">
						<?php esc_html_e( 'Category for Yandex Market', 'yml-for-yandex-market-pro' ); ?>. <a
							href="https://icopydoc.ru/para-sovetov-po-ispolzovaniyu-yml-for-yandex-market-pro/?utm_source=yml-for-yandex-market&utm_medium=documentation&utm_campaign=yml-for-yandex-market-pro&utm_content=edit-category-page&utm_term=about-category-ymarket"
							target="_blank">
							<?php esc_html_e( 'Read more', 'yml-for-yandex-market-pro' ); ?>
						</a>
					</p>
				</td>
			</tr>
			<?php
		endif;

	}

	/**
	 * Function for `create_(taxonomy)` and `edited_(taxonomy)` action-hook.
	 * 
	 * @param mixed $term_id
	 * 
	 * @return mixed
	 */
	public function save_meta_product_cat( $term_id ) {

		if ( ! isset( $_POST['yfymp_cat_meta'] ) ) {
			return;
		}
		// ? $cat_meta = array_map( 'sanitize_text_field', $_POST['yfymp_cat_meta'] );
		$cat_meta = array_map( 'trim', $_POST['yfymp_cat_meta'] );
		foreach ( $cat_meta as $key => $value ) {
			if ( empty( $value ) ) {
				delete_term_meta( $term_id, $key );
				continue;
			}
			update_term_meta( $term_id, $key, $value );
		}

		if ( isset( $_POST['yfymp_cat_meta'] ) ) {
			if ( isset( $_POST['yfymp_disabled_for_feeds'] ) ) {
				$disabled_for_feeds = array_map(
					'wp_kses_post',
					wp_unslash( $_POST['yfymp_disabled_for_feeds'] )
				);
			} else {
				$disabled_for_feeds = '';
			}
			if ( $disabled_for_feeds == '' ) {
				$disabled_for_feeds = [];
			}
			update_term_meta( $term_id, 'yfymp_disabled_for_feeds', $disabled_for_feeds );
		} else {
			update_term_meta( $term_id, 'yfymp_disabled_for_feeds', [] );
		}

		return $term_id;

	}

	/**
	 * Register bulk actions.
	 * 
	 * Function for `bulk_actions-edit-(post_type)` action-hook.
	 * 
	 * @param array $bulk_actions An array of the available bulk actions
	 * 
	 * @return array
	 */
	public function register_y4ymp_bulk_actions( $bulk_actions ) {

		$bulk_actions['add_yml_kit'] = __( 'Add to YML kit', 'yml-for-yandex-market-pro' );
		$bulk_actions['remove_yml_kit'] = __( 'Remove from YML kit', 'yml-for-yandex-market-pro' );
		$bulk_actions['remove_from_all_yml'] = __( 'Remove from all YML', 'yml-for-yandex-market-pro' );
		$bulk_actions['cancel_remove_from_all_yml'] = __( 'Cancel Remove from all YML', 'yml-for-yandex-market-pro' );
		return $bulk_actions;

	}

	/**
	 * Register bulk actions handler.
	 * 
	 * Function for `handle_bulk_actions-edit-(post_type)` action-hook.
	 * 
	 * @param string $redirect_to The redirect URL
	 * @param string $doaction The action being taken
	 * @param array $post_ids The items to take the action on. Accepts an array of IDs of posts, comments, terms, links, plugins, attachments, or users
	 * 
	 * @return string
	 */
	public function bulk_action_handler( $redirect_to, $doaction, $post_ids ) {

		if ( $doaction == 'add_yml_kit' ) {
			foreach ( $post_ids as $post_id ) {
				update_post_meta( $post_id, 'vygruzhat', 'enabled' );
			}
		} else if ( $doaction == 'remove_yml_kit' ) {
			foreach ( $post_ids as $post_id ) {
				update_post_meta( $post_id, 'vygruzhat', 'disabled' );
			}
		} else if ( $doaction == 'remove_from_all_yml' ) {
			foreach ( $post_ids as $post_id ) {
				update_post_meta( $post_id, 'yfymp_removefromxml', 'enabled' );
			}
		} else if ( $doaction == 'cancel_remove_from_all_yml' ) {
			foreach ( $post_ids as $post_id ) {
				update_post_meta( $post_id, 'yfymp_removefromxml', 'disabled' );
			}
		} else {
			return $redirect_to; // ничего не делаем если это не наше действие
		}

		$redirect_to = add_query_arg( 'y4ymp_bulk_action_done', count( $post_ids ), $redirect_to );
		return $redirect_to;

	}

	/**
	 * Выводим различные оповещения и сохраняем настройки про-версии.
	 * 
	 * Function for `admin_init` action-hook.
	 * 
	 * @return void
	 */
	public function notice_and_save_settings_pro() {

		if ( isset( $_GET['y4ymp_bulk_action_done'] ) || ! empty( $_GET['y4ymp_bulk_action_done'] ) ) {
			$data = sanitize_key( $_GET['y4ymp_bulk_action_done'] );
			new ICPD_Set_Admin_Notices(
				sprintf(
					'%s: %d',
					esc_html__( 'Processed products', 'yml-for-yandex-market-pro' ),
					intval( $data )
				),
				'success',
				true
			);
		}

		// сохранение данных в прошке
		if ( isset( $_REQUEST['y4ym_submit_action'] ) ) {
			if ( ! empty( $_POST ) && check_admin_referer( 'y4ym_nonce_action', 'y4ym_nonce_field' ) ) {
				$result_arr = [];

				if ( isset( $_GET['feed_id'] ) ) {
					$feed_id = sanitize_key( $_GET['feed_id'] );
					if ( isset( $_GET['tab'] ) && $_GET['tab'] === 'constructor_params_tab' ) {
						// вкладка конструктор параметров
						if ( ! defined( 'Y4YMP_PARAM_N' ) ) {
							define( 'Y4YMP_PARAM_N', 15 );
						}

						for ( $n = 1; $n < Y4YMP_PARAM_N; $n++ ) {
							$result_arr[ $n ] = $this->save_custom_set_arr( $n );
						}

						$opt_name = sprintf( 'y4ymp_constructor_params%s', $feed_id );
						if ( is_multisite() ) {
							update_blog_option( get_current_blog_id(), $opt_name, $result_arr );
						} else {
							update_option( $opt_name, $result_arr );
						}
					}
				}

			}
		}

	}

	/**
	 * Добавляет названия метаполей в список, чтобы стандартная фукнция базового
	 * плагина могла сохранить эти метаполя.
	 *  
	 * Function for `y4ym_f_post_meta_arrt` action-hook.
	 * 
	 * @param array $post_meta_arr Список имён метаполей плагина в карточке товара исключая массивы.
	 *
	 * @return array
	 */
	public function add_post_meta_arr( $post_meta_arr ) {

		array_push( $post_meta_arr, 'vygruzhat', 'yfymp_removefromxml' );
		return $post_meta_arr;

	}

	/**
	 * Adds settings to the product editing page.
	 * 
	 * Function for `y4ym_prepend_individual_settings_tab` action-hook.
	 * 
	 * @param WP_Post $post
	 * 
	 * @return void
	 */
	public function add_settings_individual_settings_tab( $post ) {

		if ( get_post_meta( $post->ID, 'yfymp_removefromthisyml_arr', true ) !== '' ) {
			$removefromthisyml_arr = maybe_unserialize(
				get_post_meta( $post->ID, 'yfymp_removefromthisyml_arr', true )
			);
		} else {
			$removefromthisyml_arr = [];
		}
		?>
		<div class="options_group">
			<h2>
				<strong>
					<?php esc_html_e( 'Settings for removing a product from the YML feed', 'yml-for-yandex-market-pro' ); ?>
				</strong>
			</h2>
			<?php
			woocommerce_wp_select( [
				'id' => 'vygruzhat',
				'options' => [
					'disabled' => __( 'Disabled', 'yml-for-yandex-market' ),
					'enabled' => __( 'Enabled', 'yml-for-yandex-market' )
				],
				'label' => __( 'Add to YML kit', 'yml-for-yandex-market-pro' ),
				'description' => sprintf( '<a target="_blank" href="%s/?%s">%s</a>',
					'https://icopydoc.ru/chto-takoe-yml-nabor',
					'utm_source=yml-for-yandex-market-pro&utm_medium=documentation&utm_campaign=yml-for-yandex-market-pro&utm_content=edit-product-page&utm_term=about-yml-nabor',
					__( 'Read more', 'yml-for-yandex-market-pro' )
				),
				'desc_tip' => 'true'
			] );

			woocommerce_wp_select( [
				'id' => 'yfymp_removefromxml',
				'options' => [
					'disabled' => __( 'Disabled', 'yml-for-yandex-market' ),
					'enabled' => __( 'Enabled', 'yml-for-yandex-market' )
				],
				'label' => __(
					'Forcefully remove product from all YML feeds',
					'yml-for-yandex-market-pro'
				),
				'description' => __(
					'If you check this box, the product will not be included in any feed',
					'yml-for-yandex-market-pro'
				),
				'desc_tip' => 'true'
			] );
			?>
			<p class="form-field _select_field">
				<label for="yfymp_removefromthisyml_arr">
					<?php esc_html_e( 'Forcefully remove product from selected YML feeds', 'yml-for-yandex-market-pro' ); ?>:
				</label>
				<select style="width: 100%" name="yfymp_removefromthisyml_arr[]" id="yfymp_removefromthisyml_arr" size="6"
					multiple>
					<?php
					if ( is_multisite() ) {
						$cur_blog_id = get_current_blog_id();
					} else {
						$cur_blog_id = '0';
					}
					$y4ym_settings_arr = univ_option_get( 'y4ym_settings_arr' );
					$y4ym_settings_arr_keys_arr = array_keys( $y4ym_settings_arr );
					for ( $i = 0; $i < count( $y4ym_settings_arr_keys_arr ); $i++ ) {
						$feed_id = (string) $y4ym_settings_arr_keys_arr[ $i ];
						if ( $y4ym_settings_arr[ $feed_id ]['y4ym_feed_assignment'] === '' ) {
							$feed_assignment = '';
						} else {
							$feed_assignment = sprintf( ' (%s)',
								$y4ym_settings_arr[ $feed_id ]['y4ym_feed_assignment']
							);
						}
						if ( in_array( $feed_id, $removefromthisyml_arr ) ) {
							$selected = 'selected';
						} else {
							$selected = '';
						}
						printf( '<option value="%s" %s>%s %s: feed-xml-%s.xml%s</option>',
							esc_attr( $feed_id ),
							esc_attr( $selected ),
							esc_html__( 'Feed', 'yml-for-yandex-market' ),
							esc_html( $feed_id ),
							esc_html( $cur_blog_id ),
							esc_html( $feed_assignment )
						);
					}
					?>
				</select>
			</p>
			<input type="hidden" name="_y4ymp_full_edit" value="1" />
		</div>
		<?php

	}

	/**
	 * Сохраняем данные блока, когда пост сохраняется. 
	 * Function for `save_post` action-hook.
	 * 
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated.
	 *
	 * @return void
	 */
	public function save_product( $post_id, $post, $update ) {

		if ( $post->post_type !== 'product' ) {
			return;
		} // если это не товар вукомерц
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		} // если это ревизия
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		} // если это автосохранение ничего не делаем
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		} // проверяем права юзера

		if ( isset( $_POST['_y4ymp_full_edit'] ) ) {
			if ( isset( $_POST['yfymp_removefromthisyml_arr'] ) ) {
				update_post_meta(
					$post_id,
					'yfymp_removefromthisyml_arr',
					array_map(
						'wp_kses_post',
						wp_unslash( $_POST['yfymp_removefromthisyml_arr'] )
					)
				);
			} else {
				update_post_meta(
					$post_id,
					'yfymp_removefromthisyml_arr',
					[]
				);
			}
		}

	}

	/**
	 * Cоздаём колонку. 
	 * Function for `manage_(screen_id)_columns` filter-hook.
	 * 
	 * @param string[] $columns The column header labels keyed by column ID.
	 *
	 * @return string[]
	 */
	public function add_views_column( $columns ) {

		$num = 9; // после какой по счету колонки вставлять новые
		$new_columns = [ 'y4ymp' => __( 'YML', 'yml-for-yandex-market-pro' ) ];
		return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );

	}

	/**
	 * Заполняем колонку YML данными. 
	 * Function for `manage_(post_type)_posts_custom_column` action-hook.
	 * 
	 * @param string $column_name The name of the column to display.
	 * @param int    $post_id     The current post ID.
	 *
	 * @return void
	 */
	public function fill_views_column( $column_name, $post_id ) {

		if ( $column_name === 'y4ymp' ) {
			if ( get_post_meta( $post_id, 'vygruzhat', true ) === 'enabled' ) {
				printf(
					'<div class="y4ymp_block"><div class="y4ymp_hover"><span class="dashicons dashicons-yes-alt"></span></div><span class="y4ymp_hint">%s</span></div>',
					esc_html__(
						'This product has been added to YML kit',
						'yml-for-yandex-market-pro'
					)
				);
			} else {
				printf(
					'<div class="y4ymp_block"><div class="y4ymp_hover"><span class="dashicons dashicons-marker"></span></div><span class="y4ymp_hint">%s</span></div>',
					esc_html__(
						'This product has not been added to YML kit',
						'yml-for-yandex-market-pro'
					)
				);
			}

			if ( get_post_meta( $post_id, 'yfymp_removefromxml', true ) === 'enabled' ) {
				printf(
					'<div class="y4ymp_block"><div class="y4ymp_hover"><span class="dashicons dashicons-dismiss"></span></div><span class="y4ymp_hint">%s</span></div>',
					esc_html__(
						'You have banned the transfer of data about this product to the all YML feeds',
						'yml-for-yandex-market-pro'
					)
				);
			}

			if ( get_post_meta( $post_id, 'yfymp_removefromthisyml_arr', true ) !== '' ) {
				$removefromthisyml_arr = maybe_unserialize(
					get_post_meta( $post_id, 'yfymp_removefromthisyml_arr', true )
				);
				if ( ! empty( $removefromthisyml_arr ) ) {
					printf(
						'<div class="y4ymp_block"><div class="y4ymp_hover"><span class="dashicons dashicons-warning"></span></div><span class="y4ymp_hint">%s</span></div>',
						esc_html__(
							'You have banned the transfer of data about this product to the some YML feeds',
							'yml-for-yandex-market-pro'
						)
					);
				}
			}
		}

	}

	/**
	 * Выводим различные оповещения и сохраняем настройки про-версии.
	 * 
	 * Function for `admin_init` action-hook.
	 * 
	 * @return array
	 */
	private function save_custom_set_arr( $n ) {

		$params_opt_keys_arr = [
			'param_use',
			'param_name_select', 'param_name_custom',
			'param_unit_select', 'param_unit_default_select', 'param_unit_custom',
			'param_value_select', 'param_value_custom'
		];
		$result_arr = [];
		for ( $i = 0; $i < count( $params_opt_keys_arr ); $i++ ) {
			$params_opt_name = sprintf( '%s%s', $params_opt_keys_arr[ $i ], $n );
			if ( isset( $_POST[ $params_opt_name ] ) ) {
				$params_opt_val = sanitize_text_field( $_POST[ $params_opt_name ] );
			} else {
				$params_opt_val = '';
			}
			$result_arr[ $params_opt_keys_arr[ $i ] ] = $params_opt_val;
		}
		return $result_arr;

	}

	/**
	 * Дополнительная информация для формы обратной связи.
	 * 
	 * Function for `y4ym_f_feedback_additional_info` action-hook.
	 * 
	 * @param string $additional_info
	 * 
	 * @return string
	 */
	public function feedback_additional_info( $additional_info ) {

		if ( is_multisite() ) {
			$order_id = get_blog_option( get_current_blog_id(), 'y4ymp_order_id', '' );
			$order_email = get_blog_option( get_current_blog_id(), 'y4ymp_order_email', '' );
		} else {
			$order_id = get_option( 'y4ymp_order_id', '' );
			$order_email = get_option( 'y4ymp_order_email', '' );
		}
		$additional_info .= sprintf( 'PRO: v.%s (%s / %s)',
			Y4YMP_PLUGIN_VERSION,
			$order_id,
			$order_email
		);
		return $additional_info;

	}

} // end class Y4YMP_Interface_Hoocked