<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       nlsltd.com
 * @since      1.0.0
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/includes
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
 * @since      1.0.0
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/includes
 * @author     Michael Dyer <devteam@nlsltd.com>
 */

class Ml_provisioning {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ml_provisioning_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ml_provisioning';

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
	 * - Ml_provisioning_Loader. Orchestrates the hooks of the plugin.
	 * - Ml_provisioning_i18n. Defines internationalization functionality.
	 * - Ml_provisioning_Admin. Defines all hooks for the admin area.
	 * - Ml_provisioning_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ml_provisioning-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ml_provisioning-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ml_provisioning-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ml_provisioning-public.php';

		/**
		 * The class responsible for public AJAX functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ml_provisioning-ajax.php';
		/**
		 * The class responsible for public Licence management page functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ml_provisioning-licensing.php';

		/**
		 * The class responsible for organisation details functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ml_provisioning-organisation.php';

		$this->loader = new Ml_provisioning_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ml_provisioning_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ml_provisioning_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ml_provisioning_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action('admin_init', $plugin_admin, 'ml__settings_init');
		$this->loader->add_action('admin_menu', $plugin_admin, 'ml__add_admin_menu');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		/**
		 * Public
		 */
		$plugin_public = new Ml_provisioning_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'rewrite_endpoints' );
		$this->loader->add_action( 'woocommerce_after_order_notes', $plugin_public, 'add_provisioning_checkbox' );
		$this->loader->add_action( 'woocommerce_checkout_create_order', $plugin_public, 'checkout_create_order' );
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'post_checkout_provisioning' );
		$this->loader->add_filter( 'woocommerce_account_menu_items', $plugin_public, 'account_menu_items' );

		/**
		 * Shortcodes
		 */
		$this->loader->add_shortcode( 'ml_provisioning_validate_account', $plugin_public, 'ml_provisioning_validate_to_link' );
		$this->loader->add_shortcode( 'ml_provisioning_lms_signup', $plugin_public, 'ml_provisioning_lms_signup' );
		$this->loader->add_shortcode( 'ml_provisioning_thankyou_cta', $plugin_public, 'ml_provisioning_thankyou_cta' );

		/**
		 *  Organistaion details
		 */
		$plugin_organisation = new Ml_provisioning_Organisation( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_shortcode( 'ml_organisation_details', $plugin_organisation, 'ml_organisation_details' );

		/**
		 * AJAX
		 */
		$plugin_ajax = new Ml_provisioning_Ajax( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_ajax_process_forms', $plugin_ajax, 'process_forms' );
		$this->loader->add_action( 'wp_ajax_nopriv_process_forms', $plugin_ajax, 'process_forms' );

		/**
		 * License management
		 */
		$plugin_licensing = new Ml_provisioning_Licensing( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'woocommerce_account_licences_endpoint', $plugin_licensing, 'display_licences' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ml_provisioning_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
