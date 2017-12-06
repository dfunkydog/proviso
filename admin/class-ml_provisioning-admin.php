<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       nlsltd.com
 * @since      1.0.0
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/admin
 * @author     Michael Dyer <devteam@nlsltd.com>
 */

class Ml_provisioning_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ml_provisioning_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ml_provisioning_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ml_provisioning-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ml_provisioning_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ml_provisioning_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ml_provisioning-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 *  Adds an options menu item in dashboard->settings
	 */
	public function ml__add_admin_menu()
	{

		add_options_page( 'Provisioning Options', 'Provisioning Options', 'manage_options', 'ml_provisioning', array($this, 'ml__options_page') );
	}

	/**
	 * Set up admin option fields & sections
	 */
	public function ml__settings_init()
	{
		register_setting( 'provisioning', 'ml__settings' );

		add_settings_section(
			'ml__provisioning_section',
			__( 'Your section description', 'me_learning' ),
			array($this, 'ml__settings_section_callback'),
			'provisioning'
		);

		add_settings_field(
			'ml__api_key',
			__( 'Api Key', 'me_learning' ),
			array($this, 'ml__api_key_render'),
			'provisioning',
			'ml__provisioning_section'
		);

		add_settings_field(
			'ml__api_secret',
			__( 'Api secret', 'me_learning' ),
			array($this,'ml__api_secret_render'),
			'provisioning',
			'ml__provisioning_section'
		);

		add_settings_field(
			'subdomain',
			__( 'Subdomain', 'me_learning' ),
			array($this,'subdomain_render'),
			'provisioning',
			'ml__provisioning_section'
		);

		add_settings_field(
			'source',
			__( 'Source', 'me_learning' ),
			array($this,'source_render'),
			'provisioning',
			'ml__provisioning_section'
		);
	}


	function ml__api_key_render()
	{
		$options = get_option( 'ml__settings' );
		?>
		<input type='text' name='ml__settings[ml__api_key]' value='<?php echo $options['ml__api_key']; ?>'>
		<?php
	}


	function ml__api_secret_render()
	{
		$options = get_option( 'ml__settings' );
		?>
		<input type='text' name='ml__settings[ml__api_secret]' value='<?php echo $options['ml__api_secret']; ?>'>
		<?php
	}

	function subdomain_render()
	{
		$options = get_option( 'ml__settings' );
		?>
		<input type='text' name='ml__settings[subdomain]' value='<?php echo $options['subdomain']; ?>'>
		<?php
	}

	function source_render()
	{
		$options = get_option( 'ml__settings' );
		?>
		<input type='text' name='ml__settings[source]' value='<?php echo $options['source']; ?>'>
		<?php
	}


	function ml__settings_section_callback(  )
	{
		echo __( 'This section description', 'me_learning' );
	}


	function ml__options_page(  )
	{
		//TODO : Use autoloader
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ml_provisioning-admin-display.php';
	}

}
