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



require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/includes/class-ml_provisioning-requests.php';


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

	private $make_request;

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
		$this->make_request = new Ml_provisioning_Requests;
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
		wp_localize_script($this->plugin_name, 'proviso', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( $this->plugin_name )
	));

	}

	public function rewrite_endpoints(){
		add_rewrite_endpoint('licences', EP_PAGES);
	}

	/**
	 * Display licence management page content
	 */
	public function licences_endpoint_content()
	{
		if($this->make_request->hub_is_account_linked()){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/ml_license_management.php';
		} else {
			echo "<h2>Link accounts before showing licenes</h2>";
		}
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
			'licences'    => __( 'licences', 'woocommerce' )
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
	 * Get User data
	 * @param integer $order_id Order id of current provisioning flow
	 */
	function post_checkout_provisioning($order_id)
	{
		$user_data = $this->make_request->hub_get_user();
		if( $user_data ){
			//yes : Add orders items to licensing hub as unassigned
			$this->hub_add_licences($user_data->id, $order_id);
		} else {
			//User does not exist so create licensing hub user
			$this->hub_create_user();
		}
	}

	/**
	 * Create hub account from wordpress credentials
	 * @param integer Woocommerce order ID
	 */
	private function hub_create_user()
	{
		$new_hub_user = $this->make_request->hub_create_user();
		if( $new_hub_user ){
			// Successful: Add products to hub under hub user's account as unassigned
			// add licence uhb id to wordpres user meta
			//update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
			$this->make_request->hub_add_licences($new_hub_user->id, $order_id);
		} else {
			// Failed: Handle error
			$this->error_handler($new_hub_user);
		}
	}

	/**
	 * Add recently purchases courses to licensing hub
	 * @param string $user_id The user id from licensing hub
	 * @param string $order_id The woocommerce order id
	 */
	private function hub_add_licences($user_id, $order_id)
	{
		$added_products = $this->make_request->hub_add_licences($user_id, $order_id);
		//success
		$this->link_lms_account();
		// TODO If any of the product licenses Fail : Handle failure
	}

	/**
	 * Kickstarts process of linking wordpress user email to lms account
	 */
	public function link_lms_account()
	{
		if( $this->make_request->hub_is_account_linked() ){
			echo " E";
		} else {
			if( $this->make_request->subdomain_contains_wp_email() ) {
				wp_redirect('/link-accounts');
				exit;
				// $this->ml_provisioning_validate_to_link();
			} else {
				//create new LMS user
				if( $this->make_request->subdomain_create_user_account() );
			}
		}
	}

	public function ml_provisioning_validate_to_link()
	{
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/ml_validate-to-link.php';
	}

	/**
	 * Returns [ml_provisioning_thankyou_cta] shortcode content
	 */
	public function ml_provisioning_thankyou_cta($atts)
	{
		$licenses_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'licences';
		if('' !== wc_get_order($atts['order'])->get_meta('allocate')) {
			return  "<a href={$licenses_url} class='button -lime'>Login to training</a>";
		} else {
			return "<a href={$licenses_url} class='button -lime'>Manage Licenses</a>";
		}
	}

}

