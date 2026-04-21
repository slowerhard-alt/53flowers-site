<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 * @version    6.0.9 (02-06-2025)
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/admin
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
class Y4YMP_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 0.1.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Y4yms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Y4yms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/y4ymp-admin.css', [], $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Y4yms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Y4yms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/y4ymp-admin.js', [ 'jquery' ], $this->version, false );

		// Возможность выбора только одной категории товара
		$screen = get_current_screen(); // определяем, на какой странице находимся
		// для страниц добавления и редактирования товара
		if (
			( 'add' === $screen->action && 'product' === $screen->post_type )
			|| 'product' === $screen->id
		) {
			wc_enqueue_js( "
			$('#ymarket-all input:checkbox').change( function() {
				var max = 1,
				    count = $('#ymarket-all input:checked').length;
				if (count > max) {
					$(this).prop( 'checked', '' );
					alert( '" . esc_html__( 'You can only select one category at a time', 'yml-for-yandex-market-pro' ) . ".' );
				}
			});
		" );
		}

		// для быстрого редактирования товара
		if ( 'edit-product' === $screen->id ) {
			wc_enqueue_js( "
			$('.ymarket-checklist input:checkbox').change( function() {
				var max = 1,
				    count = $('.ymarket-checklist input:checked').length;
				if (count > max) {
					$(this).prop( 'checked', '' );
					alert( '" . esc_html__( 'You can only select one category at a time', 'yml-for-yandex-market-pro' ) . ".' );
				}
			});
		" );
		}
		// end Возможность выбора только одной категории товара

	}

	/**
	 * Register the classes for the admin area.
	 *
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public function enqueue_classes() {

		$args = [ 
			'pref' => 'y4ymp',
			'slug' => Y4YMP_PLUGIN_SLUG,
			'plugin_slug' => Y4YMP_PLUGIN_BASENAME,
			'premium_version' => Y4YMP_PLUGIN_VERSION
		];
		new Y4YM_Plugin_Upd( $args );
		new Y4YM_Plugin_Form_Activate( 'y4ymp', Y4YMP_PLUGIN_SLUG );

		new Y4YMP_Metaboxes();
		new Y4YMP_Interface_Hoocked();
		new Y4YMP_Generation_Hoocked();

	}

	/**
	 * Set the plugin settings.
	 * Function for `y4ym_f_set_default_feed_settings_result_arr` action-hook.
	 * 
	 * @param array $result_arr
	 * 
	 * @return array
	 */
	public function set_plugin_settings( $result_arr ) {

		$default_data_arr = self::get_plugin_data();
		for ( $i = 0; $i < count( $default_data_arr ); $i++ ) {
			array_push( $result_arr, $default_data_arr[ $i ] );
		}
		$result_arr = $this->change_data_arr( $result_arr );
		return $result_arr;

	}

	/**
	 * Get the plugin data.
	 * 
	 * @return array
	 */
	public function get_plugin_data() {

		$dara_arr_obj = new Y4YMP_Data();
		$result_arr = $dara_arr_obj->get_data_arr();
		return $result_arr;

	}

	/**
	 * Этой функцией мы добавляем пункты в селекты для уже существующих опций. Также этой функцией удобно 
	 * встявлять новые пукнты настроек между существующими пунктами.
	 * 
	 * @param array $result_arr
	 * 
	 * @return array
	 */
	private function change_data_arr( $result_arr ) {

		for ( $i = 0; $i < count( $result_arr ); $i++ ) {
			if ( $result_arr[ $i ]['opt_name'] === 'y4ym_whot_export' ) {
				$result_arr[ $i ]['data']['key_value_arr'][] = [ 
					'value' => 'vygruzhat',
					'text' => sprintf( '%s (Premium)',
						__( 'Only products from YML kit', 'yml-for-yandex-market-pro' )
					)
				];
				$result_arr[ $i ]['data']['key_value_arr'][] = [ 
					'value' => 'collections',
					'text' => sprintf( '%s "%s" (Premium)',
						__( 'Only products from', 'yml-for-yandex-market-pro' ),
						__( 'Сollections for YML feed', 'yml-for-yandex-market' )
					)
				];
			}
		}
		return $result_arr;

	}

	/**
	 * Add new taxonomy.
	 * 
	 * @return void
	 */
	public function add_new_taxonomies() {

		$labels_arr = [ 
			'name' => __( 'Categories for Yandex Market', 'yml-for-yandex-market-pro' ),
			'singular_name' => 'Category',
			'search_items' => __( 'Search Category', 'yml-for-yandex-market-pro' ),
			'popular_items' => null, // __('Популярные категории', 'yml-for-yandex-market-pro'),
			'all_items' => __( 'All Categories', 'yml-for-yandex-market-pro' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Category', 'yml-for-yandex-market-pro' ),
			'update_item' => __( 'Update Category', 'yml-for-yandex-market-pro' ),
			'add_new_item' => __( 'Add New Category', 'yml-for-yandex-market-pro' ),
			'new_item_name' => __( 'New Category Name', 'yml-for-yandex-market-pro' ),
			'menu_name' => __( 'Categories for Yandex Market', 'yml-for-yandex-market-pro' )
		];
		$args_arr = [ 
			'hierarchical' => true, // true - по типу рубрик, false - по типу меток (по умолчанию)
			'labels' => $labels_arr,
			'public' => true, // каждый может использовать таксономию, либо только администраторы, по умолчанию - true
			'show_ui' => true, // добавить интерфейс создания и редактирования
			'publicly_queryable' => false, // сделать элементы таксономии доступными для добавления в меню сайта. По умолчанию: значение аргумента public.
			'show_in_nav_menus' => false, // добавить на страницу создания меню
			'show_tagcloud' => false, // нужно ли разрешить облако тегов для этой таксономии
			'update_count_callback' => '_update_post_term_count', // callback-функция для обновления счетчика $object_type
			'query_var' => true, // разрешено ли использование query_var, также можно указать строку, которая будет использоваться в качестве него, по умолчанию - имя таксономии
			'rewrite' => [ // настройки URL пермалинков
				'slug' => 'ymarket', // ярлык
				'hierarchical' => true // разрешить вложенность
			]
		];
		register_taxonomy( 'ymarket', [ 'product' ], $args_arr );

	}

}
