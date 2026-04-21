<?php

/**
 * This class is responsible for the "Constructor params" tab.
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 * @version    6.0.13 (25-08-2025)
 * @see        https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html 
 *             https://wp-kama.ru/function/wp_list_table
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/admin
 */

/**
 * This class is responsible for the "Constructor params" tab.
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/admin/partials/settings-page
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
class Y4YMP_Constructor_Params_WP_List_Table extends WP_List_Table {

	/**
	 * Feed ID.
	 * @var string
	 */
	private $feed_id;

	/**
	 * Constructor.
	 * 
	 * @param string $feed_id
	 */
	public function __construct( $feed_id ) {

		$this->feed_id = $feed_id;
		global $status, $page;
		parent::__construct( [
			// По умолчанию: '' ($this->screen->base);
			// Название для множественного числа, используется во всяких заголовках, например в css классах,
			// в заметках, например 'posts', тогда 'posts' будет добавлен в класс table
			'plural' => '',

			// По умолчанию: ''; Название для единственного числа, например 'post'. 
			'singular' => '',

			// По умолчанию: false; Должна ли поддерживать таблица AJAX. Если true, класс будет вызывать метод 
			// _js_vars() в подвале, чтобы передать нужные переменные любому скрипту обрабатывающему AJAX события.
			'ajax' => false,

			// По умолчанию: null; Строка содержащая название хука, нужного для определения текущей страницы. 
			// Если null, то будет установлен текущий экран.
			'screen' => null
		] );

	}

	/**
	 * Метод get_columns() необходим для маркировки столбцов внизу и вверху таблицы.
	 * 
	 * Ключи в массиве должны быть теми же, что и в массиве данных, иначе соответствующие столбцы
	 * не будут отображены.
	 * 
	 * @return array
	 */
	public function get_columns() {

		$columns = [
			'y4ymp_constructor_param_switcher' => __( 'Use', 'yml-for-yandex-market-pro' ),
			'y4ymp_constructor_param_name' => 'Name (X)',
			'y4ymp_constructor_param_unit' => 'Unit (Y)',
			'y4ymp_constructor_param_value' => 'Value (Z)',

		];
		return $columns;

	}

	/**	
	 * Метод вытаскивает из БД данные, которые будут лежать в таблице $this->table_data();
	 * 
	 * @param array $table_data_arr
	 * 
	 * @return array
	 */
	private function table_data( $table_data_arr = [] ) {

		if ( ! defined( 'Y4YMP_PARAM_N' ) ) {
			define( 'Y4YMP_PARAM_N', 14 );
		}

		$opt_name = sprintf( 'y4ymp_constructor_params%s', $this->get_feed_id() );
		if ( is_multisite() ) {
			$constructor_params_arr = get_blog_option( get_current_blog_id(), $opt_name, [] );
		} else {
			$constructor_params_arr = get_option( $opt_name, [] );
		}

		for ( $i = 1; $i < Y4YMP_PARAM_N; $i++ ) {
			if ( ! isset( $constructor_params_arr[ $i ] ) ) {
				$constructor_params_arr[ $i ] = [];
			}
			$result_arr[] = [
				'y4ymp_constructor_param_switcher' => sprintf( '%s <br/>Param %s: %s',
					'<strong>&lt;param name=&quot;X&quot; unit=&quot;Y&quot;&gt;Z&lt;/param&gt;</strong>',
					$i,
					$this->get_select_html_v2(
						'param_use' . $i,
						array_key_exists( 'param_use', $constructor_params_arr[ $i ] ) ? $constructor_params_arr[ $i ]['param_use'] : 'disabled',
						$this->get_feed_id(),
						[
							'disabled' => __( 'Disabled', 'yml-for-yandex-market-pro' ),
							'enabled' => __( 'Enabled', 'yml-for-yandex-market-pro' )
						]
					)
				),
				'y4ymp_constructor_param_name' => sprintf( '%s<br />%s<br />%s',
					$this->get_select_html_v2(
						'param_name_select' . $i,
						array_key_exists( 'param_name_select', $constructor_params_arr[ $i ] ) ? $constructor_params_arr[ $i ]['param_name_select'] : 'Размер',
						$this->get_feed_id(),
						[
							'Размер' => 'Размер',
							'Обхват груди' => 'Обхват груди',
							'Обхват талии' => 'Обхват талии',
							'Обхват бедер' => 'Обхват бедер',
							'Рост' => 'Рост',
							'Длина шагового шва' => 'Длина шагового шва',
							'Обхват под грудью' => 'Обхват под грудью',
							'Размер чашки' => 'Размер чашки',
							'Размер трусов' => 'Размер трусов',
							'Обхват ладони' => 'Обхват ладони',
							'Возраст' => 'Возраст',
							'Объем' => 'Объем',
						]
					),
					__( 'or specify custom value', 'yml-for-yandex-market-pro' ),
					$this->get_input_html(
						'param_name_custom' . $i,
						array_key_exists( 'param_name_custom', $constructor_params_arr[ $i ] ) ? $constructor_params_arr[ $i ]['param_name_custom'] : '',
						$this->get_feed_id(),
						'type2'
					)
				),
				'y4ymp_constructor_param_unit' => sprintf( '%s<br />%s:<br />%s<br />%s:<br />%s',
					$this->get_select_html(
						'param_unit_select' . $i,
						array_key_exists( 'param_unit_select', $constructor_params_arr[ $i ] ) ? $constructor_params_arr[ $i ]['param_unit_select'] : 'disabled',
						$this->get_feed_id(),
						[]
					),
					__( 'In the absence of a substitute', 'yml-for-yandex-market-pro' ),
					$this->get_select_html_v2(
						'param_unit_default_select' . $i,
						array_key_exists( 'param_unit_default_select', $constructor_params_arr[ $i ] ) ? $constructor_params_arr[ $i ]['param_unit_default_select'] : 'AU',
						$this->get_feed_id(),
						[
							'AU' => 'AU',
							'DE' => 'DE',
							'EU' => 'EU',
							'FR' => 'FR',
							'Japan' => 'Japan',
							'INT' => 'INT',
							'IT' => 'IT',
							'RU' => 'RU',
							'UK' => 'UK',
							'US' => 'US',
							'INCH' => 'INCH',
							'Height' => 'Height',
							'Months' => 'Months',
							'Round' => 'Round',
							'Years' => 'Years',
							'ml' => 'ml',
							'мл' => 'мл'
						]
					),
					__( 'or specify custom value', 'yml-for-yandex-market-pro' ),
					$this->get_input_html(
						'param_unit_custom' . $i,
						array_key_exists( 'param_unit_custom', $constructor_params_arr[ $i ] ) ? $constructor_params_arr[ $i ]['param_unit_custom'] : '',
						$this->get_feed_id(),
						'type2'
					)
				),
				'y4ymp_constructor_param_value' => sprintf( '%s<br />%s:<br />%s',
					$this->get_select_html(
						'param_value_select' . $i,
						array_key_exists( 'param_value_select', $constructor_params_arr[ $i ] ) ? $constructor_params_arr[ $i ]['param_value_select'] : 'disabled',
						$this->get_feed_id(),
						[]
					),
					__( 'or specify custom value', 'yml-for-yandex-market-pro' ),
					$this->get_input_html(
						'param_value_custom' . $i,
						array_key_exists( 'param_value_custom', $constructor_params_arr[ $i ] ) ? $constructor_params_arr[ $i ]['param_value_custom'] : '',
						$this->get_feed_id(),
						'type2'
					)
				)
			];
		}
		return $result_arr;

	}

	/**	
	 * Метод возвращает html-тег input.
	 * 
	 * @param string $opt_name
	 * @param string $opt_value
	 * @param string $feed_id
	 * @param string $type_placeholder Может быть: `type1`, `type2`, `type3`, `type4`, `type5`.
	 * 
	 * @return string
	 */
	private function get_input_html( $opt_name, $opt_value, $feed_id = '1', $type_placeholder = 'type1' ) {

		switch ( $type_placeholder ) {
			case 'type1':
				$placeholder = __( 'Name post_meta', 'yml-for-yandex-market-pro' );
				break;
			case 'type2':
				$placeholder = __( 'Default value', 'yml-for-yandex-market-pro' );
				break;
			case 'type3':
				$placeholder = __( 'Value', 'yml-for-yandex-market-pro' ) . ' / ' . __( 'Name post_meta', 'yml-for-yandex-market-pro' );
				break;
			case 'type4':
				$placeholder = __( 'Name post_meta', 'yml-for-yandex-market-pro' ) . ' ' . __( 'for simple products', 'yml-for-yandex-market-pro' );
				break;
			case 'type5':
				$placeholder = __( 'Name post_meta', 'yml-for-yandex-market-pro' ) . ' ' . __( 'for variable products', 'yml-for-yandex-market-pro' );
				break;
			default:
				$placeholder = __( 'Name post_meta', 'yml-for-yandex-market-pro' );
		}

		return '<input type="text" maxlength="25" name="' . $opt_name . '" id="' . $opt_name . '" value="' . $opt_value . '" placeholder="' . $placeholder . '" />';

	}

	/**	
	 * Метод возвращает html-тег select.
	 * 
	 * @param string $opt_name
	 * @param string $opt_value
	 * @param string $feed_id
	 * @param array $otions_arr
	 * 
	 * @return string
	 */
	private function get_select_html_v2( $opt_name, $opt_value, $feed_id = '1', $otions_arr = [] ) {

		$res = new Y4YM_Get_Open_Tag(
			'select',
			[
				'name' => $opt_name,
				'id' => $opt_name
			]
		);
		foreach ( $otions_arr as $key => $value ) {
			$res .= '<option value="' . $key . '" ' . selected( $opt_value, $key, false ) . '>' . $value . '</option>';
		}
		$res .= new Y4YM_Get_Closed_Tag( 'select' );
		return $res;

	}

	/**	
	 * Метод возвращает html-тег select.
	 * 
	 * @param string $opt_name
	 * @param string $opt_value
	 * @param string $feed_id
	 * @param array $otions_arr
	 * 
	 * @return string
	 */
	private function get_select_html( $opt_name, $opt_value, $feed_id = '1', $otions_arr = [] ) {

		$res = '<select name="' . $opt_name . '" id="' . $opt_name . '">
					<option value="disabled" ' . selected( $opt_value, 'disabled', false ) . '>' . __( 'Disabled', 'yml-for-yandex-market-pro' ) . '</option>';

		if ( isset( $otions_arr['products_id'] ) ) {
			$res .= '<option value="products_id" ' . selected( $opt_value, 'products_id', false ) . '>' . __( 'Add from products ID', 'yml-for-yandex-market-pro' ) . '</option>';
		}

		if ( isset( $otions_arr['yes'] ) ) {
			$res .= '<option value="yes" ' . selected( $opt_value, 'yes', false ) . '>' . __( 'Yes', 'yml-for-yandex-market-pro' ) . '</option>';
		}

		if ( isset( $otions_arr['no'] ) ) {
			$res .= '<option value="no" ' . selected( $opt_value, 'no', false ) . '>' . __( 'No', 'yml-for-yandex-market-pro' ) . '</option>';
		}

		if ( isset( $otions_arr['true'] ) ) {
			$res .= '<option value="true" ' . selected( $opt_value, 'true', false ) . '>' . __( 'True', 'yml-for-yandex-market-pro' ) . '</option>';
		}

		if ( isset( $otions_arr['false'] ) ) {
			$res .= '<option value="false" ' . selected( $opt_value, 'false', false ) . '>' . __( 'False', 'yml-for-yandex-market-pro' ) . '</option>';
		}

		if ( isset( $otions_arr['alltrue'] ) ) {
			$res .= '<option value="alltrue" ' . selected( $opt_value, 'alltrue', false ) . '>' . __( 'Add to all', 'yml-for-yandex-market-pro' ) . ' true</option>';
		}

		if ( isset( $otions_arr['allfalse'] ) ) {
			$res .= '<option value="allfalse" ' . selected( $opt_value, 'allfalse', false ) . '>' . __( 'Add to all', 'yml-for-yandex-market-pro' ) . ' false</option>';
		}

		if ( isset( $otions_arr['sku'] ) ) {
			$res .= '<option value="sku" ' . selected( $opt_value, 'sku', false ) . '>' . __( 'Substitute from SKU', 'yml-for-yandex-market-pro' ) . '</option>';
		}

		if ( isset( $otions_arr['post_meta'] ) ) {
			$res .= '<option value="post_meta" ' . selected( $opt_value, 'post_meta', false ) . '>' . __( 'Substitute from post meta', 'yml-for-yandex-market-pro' ) . '</option>';
		}

		if ( isset( $otions_arr['default_value'] ) ) {
			$res .= '<option value="default_value" ' . selected( $opt_value, 'default_value', false ) . '>' . __( 'Default value from field', 'yml-for-yandex-market-pro' ) . ' "' . __( 'Default value', 'yml-for-yandex-market-pro' ) . '"</option>';
		}

		if ( class_exists( 'WooCommerce_Germanized' ) ) {
			if ( isset( $otions_arr['germanized'] ) ) {
				$res .= '<option value="germanized" ' . selected( $opt_value, 'germanized', false ) . '>' . __( 'Substitute from', 'yml-for-yandex-market-pro' ) . 'WooCommerce Germanized</option>';
			}
		}

		if ( isset( $otions_arr['brands'] ) ) {
			if ( is_plugin_active( 'perfect-woocommerce-brands/perfect-woocommerce-brands.php' )
				|| is_plugin_active( 'perfect-woocommerce-brands/main.php' )
				|| class_exists( 'Perfect_Woocommerce_Brands' ) ) {
				$res .= '<option value="sfpwb" ' . selected( $opt_value, 'sfpwb', false ) . '>' . __( 'Substitute from', 'yml-for-yandex-market-pro' ) . 'Perfect Woocommerce Brands</option>';
			}
			if ( is_plugin_active( 'saphali-custom-brands-pro/saphali-custom-brands-pro.php' ) ) {
				$res .= '<option value="saphali_brands" ' . selected( $opt_value, 'saphali_brands', false ) . '>' . __( 'Substitute from', 'yml-for-yandex-market-pro' ) . 'Saphali Custom Brands Pro</option>';
			}
			if ( is_plugin_active( 'premmerce-woocommerce-brands/premmerce-brands.php' ) ) {
				$res .= '<option value="premmercebrandsplugin" ' . selected( $opt_value, 'premmercebrandsplugin', false ) . '>' . __( 'Substitute from', 'yml-for-yandex-market-pro' ) . 'Premmerce Brands for WooCommerce</option>';
			}
			if ( is_plugin_active( 'woocommerce-brands/woocommerce-brands.php' ) ) {
				$res .= '<option value="plugin_woocommerce_brands" ' . selected( $opt_value, 'plugin_woocommerce_brands', false ) . '>' . __( 'Substitute from', 'yml-for-yandex-market-pro' ) . 'WooCommerce Brands</option>';
			}
			if ( class_exists( 'woo_brands' ) ) {
				$res .= '<option value="woo_brands" ' . selected( $opt_value, 'woo_brands', false ) . '>' . __( 'Substitute from', 'yml-for-yandex-market-pro' ) . 'Woocomerce Brands Pro</option>';
			}
			if ( is_plugin_active( 'yith-woocommerce-brands-add-on/init.php' ) ) {
				$res .= '<option value="yith_woocommerce_brands_add_on" ' . selected( $opt_value, 'yith_woocommerce_brands_add_on', false ) . '>' . __( 'Substitute from', 'yml-for-yandex-market-pro' ) . 'YITH WooCommerce Brands Add-On</option>';
			}
		}

		foreach ( get_woo_attributes() as $attribute ) {
			$res .= '<option value="' . $attribute['id'] . '" ' . selected( $opt_value, $attribute['id'], false ) . '>' . $attribute['name'] . '</option>';
		}
		$res .= '</select>';
		return $res;

	}

	/**
	 * Prepares the list of items for displaying.
	 * 
	 * Метод prepare_items определяет два массива, управляющие работой таблицы:
	 * `$hidden` - определяет скрытые столбцы
	 * `$sortable` - определяет, может ли таблица быть отсортирована по этому столбцу.
	 * 
	 * @see https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html#screen-options
	 *
	 * @return void
	 */
	public function prepare_items() {

		$columns = $this->get_columns();
		$hidden = [];
		$sortable = $this->get_sortable_columns(); // вызов сортировки
		$this->_column_headers = [ $columns, $hidden, $sortable ];
		// блок пагинации пропущен
		$this->items = $this->table_data();

	}

	/**
	 * Данные таблицы.
	 * 
	 * Наконец, метод назначает данные из примера на переменную представления данных класса — items.
	 * Прежде чем отобразить каждый столбец, WordPress ищет методы типа column_{key_name}, например, 
	 * function column_html_feed_url. Такой метод должен быть указан для каждого столбца. Но чтобы не создавать 
	 * эти методы для всех столбцов в отдельности, можно использовать column_default. Эта функция обработает все 
	 * столбцы, для которых не определён специальный метод
	 * 
	 * @param object|array $item
	 * @param string $column_name
	 * 
	 * @return string
	 */
	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'y4ymp_constructor_param_switcher':
			case 'y4ymp_constructor_param_name':
			case 'y4ymp_constructor_param_unit':
			case 'y4ymp_constructor_param_value':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); // Мы отображаем целый массив во избежание проблем
		}

	}

	/**
	 * Get FeedID.
	 * 
	 * @return string
	 */
	private function get_feed_id() {
		return $this->feed_id;
	}

}