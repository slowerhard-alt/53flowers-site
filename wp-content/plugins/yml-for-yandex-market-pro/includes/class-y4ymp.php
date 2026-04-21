<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 * @version    6.0.0 (20-03-2025)
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    Y4YMP
 * @subpackage Y4YMP/includes
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
class Y4YMP {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var Y4YMP_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		if ( defined( 'Y4YMP_PLUGIN_VERSION' ) ) {
			$this->version = Y4YMP_PLUGIN_VERSION;
		} else {
			$this->version = '0.1.0';
		}
		$this->plugin_name = 'yml-for-yandex-market-pro';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Y4YMP_Data. Defines all the plugin data in database.
	 * - Y4YMP_Loader. Orchestrates the hooks of the plugin.
	 * - Y4YMP_i18n. Defines internationalization functionality.
	 * - Y4YMP_Admin. Defines all hooks for the admin area.
	 * - Y4YMP_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 0.1.0
	 * @access private
	 * 
	 * @return void
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for set and get the plugin data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-y4ymp-data.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-y4ymp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-y4ymp-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-y4ymp-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-y4ymp-public.php';

		$this->loader = new Y4YMP_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Y4YMP_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 0.1.0
	 * @access private
	 * 
	 * @return void
	 */
	private function set_locale() {

		$plugin_i18n = new Y4YMP_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 0.1.0
	 * @access private
	 * 
	 * @return void
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Y4YMP_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// вызываем служебные классы в админке
		$this->loader->add_action( 'init', $plugin_admin, 'enqueue_classes' );

		// добавляем новую таксономию для альтернативного дерева категорий
		$this->loader->add_action( 'init', $plugin_admin, 'add_new_taxonomies' );

		// добавляем новые данные в таблицу настроек базового плагина
		$this->loader->add_action(
			'y4ym_f_set_default_feed_settings_result_arr',
			$plugin_admin,
			'set_plugin_settings',
			2
		);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since 0.1.0
	 * @access private
	 * 
	 * @return void
	 */
	private function define_public_hooks() {

		$plugin_public = new Y4YMP_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.1.0
	 * 
	 * @return void
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 0.1.0
	 * 
	 * @return string The name of the plugin. For example: `yml-for-yandex-market-pro`.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 0.1.0
	 * 
	 * @return Y4YMP_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 0.1.0
	 * 
	 * @return string The version number of the plugin. For example: `0.1.0`.
	 */
	public function get_version() {
		return $this->version;
	}

}
