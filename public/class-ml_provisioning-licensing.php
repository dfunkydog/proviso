<?php

	/**
	 * The public-facing LICENSING functionality.
	 *
	 * Creates the various functions used for LICENSING on the front-end.
	 *
	 * @package    Plugin
	 * @subpackage Plugin/public
	 * @author     Plugin_Author <michael@nlsltd.com>
	 */

	if( ! class_exists( 'Ml_provisioning_Licensing' ) ){

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/includes/class-ml_provisioning-requests.php';

		class Ml_provisioning_Licensing {

			private $make_request;
			private $plugin_name;

			public function __construct( $plugin_name, $version )
			{

				$this->plugin_name = $plugin_name;
				$this->version = $version;
				$this->make_request = new Ml_provisioning_Requests;
			}

			/**
			 * Display licensing management page content
			 */
			function display_licences(){
				$licenses = $this->make_request->hub_get_licences(get_user_meta(get_current_user_id(), 'ml_license_hub_id', true));
				require_once plugin_dir_path( dirname(__FILE__) ) . 'public/partials/ml_license_management.php';
			}
		}

	}
