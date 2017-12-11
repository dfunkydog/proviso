<?php

	/**
	 * The public-facing AJAX functionality.
	 *
	 * Creates the various functions used for AJAX on the front-end.
	 *
	 * @package    Plugin
	 * @subpackage Plugin/public
	 * @author     Plugin_Author <email@example.com>
	 */

	if( ! class_exists( 'Ml_provisioning_Ajax' ) ){

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/includes/class-ml_provisioning-requests.php';

		class Ml_provisioning_Ajax {

		private $make_request;
		private $plugin_name;

		public function __construct( $plugin_name, $version )
		{

			$this->plugin_name = $plugin_name;
			$this->version = $version;
			$this->make_request = new Ml_provisioning_Requests;
			$this->response = array();
		}

		/**
		 * An  AJAX callback.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function process_forms()
		{
			// Check the nonce for permission.
			if( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], $this->plugin_name ) ) {
				die( 'Permission Denied' );
			}

			$this->handle_form($_POST['callback']);

		}

		/**
		 * Process form data. The actual process depends on the form callback
		 * variable taken from <code>$_POST['callback']</code>
		 */
		private function handle_form($form)
		{
			switch ($form) {
				case 'validate-to-link':
					return $this->process_validate_LMS_user();
					break;
				default:
					return null;
					break;
			}
		}

		/**
		 * Validate credentials with LMS
		 */
		function process_validate_LMS_user(){
			$user_name = (isset($_POST['username']) && $_POST['username'] !== '') ?: false;
			$user_pass = (isset($_POST['password']) && $_POST['password'] !== '') ?: false;
			if(!$user_name || !$user_pass){
				$this->response = array(
					'status'  => 200,
					'content' => array(
						'state' => 'error',
						'message' => 'Username or password missing',
					)
				);
				$this->feedback();
			} else {
				$account_validated =  $this->make_request->subdomain_validate_user_account($_POST['username'], $_POST['password']);
				if(true == $account_validated){
					$this->link_validated_account();
				}
			}
		}

		/**
		 * Link validate user accounts
		 *
		 */
		function link_validated_account()
		{
			if($this->make_request->link_user_accounts() ) {
				$this->response = array(
					'status'  => 200,
					'content' => array(
						'state' => 'account_linked',
						'message' => 'Account successfully Linked',
					)
				);
			} else {
					$this->response = array(
						'status'  => 200,
						'content' => array(
							'state' => 'error',
							'message' => 'There was a problem linking your account. Please contact our Admins',
						)
					);
			}
			$this->feedback();
		}

		/**
		 * Terminates and returns xhr response
		 */
		function feedback()
		{
			wp_die( json_encode($this->response) );
		}
	}

}

