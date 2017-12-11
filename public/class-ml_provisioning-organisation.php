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


require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/includes/class-ml_provisioning-requests.php';

class Ml_provisioning_Organisation {

	private $make_request;
	private $plugin_name;

	public function __construct( $plugin_name, $version )
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->make_request = new Ml_provisioning_Requests;
	}

	/**
	 * Organistaion Details shortcode output
	 */
	public function ml_organisation_details($atts)
	{
		require_once plugin_dir_path( dirname(__FILE__) ) . 'public/partials/ml_organisation-details.php';

	}
}
