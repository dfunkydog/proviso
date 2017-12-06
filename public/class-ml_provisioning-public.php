<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       nlsltd.com
 * @since      1.0.0
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/public
 * @author     Michael Dyer <devteam@nlsltd.com>
 */

class Ml_provisioning_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ml_provisioning-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ml_provisioning-public.js', array( 'jquery' ), $this->version, false );

	}

	public function rewrite_endpoints(){
		add_rewrite_endpoint('licenses', EP_PAGES);
	}

	/**
	 * Display license management page content
	 */
	public function licenses_endpoint_content()
	{
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/ml_license_management.php';
	}

	public function account_menu_items($items)
	{

		$orders = $items['orders'];
		$edit_address = $items['edit-address'];
		$edit_account = $items['edit-account'];

		$new_items_order = array(
			$orders,
			$edit_account,
			$edit_address,
			'payment-methods'    => __( 'Payment Methods', 'woocommerce' ),
			'licenses'    => __( 'licenses', 'woocommerce' )
		);
		return $new_items_order;
	}

	/**
	 * Add provisioning field to checkout
	 */
	public function add_provisioning_checkbox( $checkout )
	{
		$allocate_items = false;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ($cart_item['quantity'] > 1){
				$allocate_items = true;
				break;
			}
		}
		echo '<div id="ml_provisioning_field">';
			woocommerce_form_field( 'ml_provisioning_check', array(
				'type'          => 'checkbox',
				'class'         => 'provisioning',
				'id'         => 'ml-provisioning-check',
				'label'         => __('These courses are for:<br><span>Myself</span><span>Other People</span>'),
				'checked'         => 'checked',
				'default'         => $allocate_items,
				), $checkout->get_value( 'ml_provisioning_check' ));
		echo '</div>';
	}

	/**
	 * update order meta data from provisioning fields
	 */
	public function checkout_create_order( $order )
	{
		$val = isset($_POST['ml_provisioning_check']) ? $_POST['ml_provisioning_check'] : false;
		$order->update_meta_data('allocate', $val );
		return $order;
	}

	/**
	 * CHeck User data
	 * TODO : Change name
	 */
	function get_user_data($echo = true)
	{
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/includes/class-ml_provisioning-requests.php';

		$raw_data = new Ml_provisioning_Requests;
		$user_data = $raw_data->get_movies();
		//Does wordpress account exists in licensing hub?
		if($user_data['original_title'] === 'Fight Club'){
			//yes ->
			$this->hub_add_courses(); //TODO
		} else {
			$this->hub_create_account(); //TODO
		}
	}

	/**
	 * Create hub account from wordpress credentials
	 */
	private function hub_create_account(){
		// TODO create hub account

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/includes/class-ml_provisioning-create-account.php';
	}

	/**
	 * Add recently purchases courses to licensing hub
	 */
	private function hub_add_courses(){
		// TODO create hub account
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/includes/class-ml_provisioning-add-courses.php';
	}



}
