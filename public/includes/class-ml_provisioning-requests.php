<?php

/**
 * Raw request to licensing api.
 *
 * makes the requests to licensing api
 *
 * @link       nlsltd.com
 * @since      1.0.0
 *
 * @package    Ml_provisioning\public\includes
 * @author     Michael Dyer <devteam@nlsltd.com>
 */
class Ml_provisioning_Requests {

	public function __construct(){
		$this->options = get_option( 'ml__settings' );
	}

	/**
	 * Get current user details from $wp_user object
	 * @param string $arg
	 *
	 * @return string Retuns the user detail supplied by $wp_user
	 * valid strings are <code>email, firstname, lastname, id</code>
	 */
	private function get_user($arg)
	{
		$wp_user = wp_get_current_user();
		switch ($arg) {
			case 'email':
				return $wp_user->user_email;
				break;
			case 'firstname':
				return $wp_user->user_firstname;
				break;
			case 'lastname':
				return $wp_user->user_lastname;
				break;
			case 'id':
				return $wp_user->ID;
				break;
			case 'login':
				return $wp_user->user_login;
				break;
			case 'licenceuserid':
				$ml_license_hub_id = get_user_meta( $wp_user->ID, 'ml_license_hub_id' );
				return $ml_license_hub_id[0];
				break;
			case 'lmsuserid':
				$ml_license_hub_id = get_user_meta( $wp_user->ID, 'ml_license_subdomain_id' );
				return $ml_license_hub_id[0];
				break;

			default:
				return null;
				break;
		}
	}

	/**
	 * Add a Key, Value pair to wp user's meta.
	 * @param string $arg
	 *
	 * @return boolean >
	 */
	private function set_user_meta($key, $value)
	{
		switch ($key) {
			case 'licenceuserid':
				if(get_user_meta($this->get_user('id'), 'ml_license_hub_id') !== $value){
					return update_user_meta( $this->get_user('id'), 'ml_license_hub_id', $value );
				} else {
					return false;
				}
			case 'lmsuserid':
				if(get_user_meta($this->get_user('id'), 'ml_license_subdomain_id') !== $value){
					return update_user_meta( $this->get_user('id'), 'ml_license_subdomain_id', $value );
				} else {
					return false;
				}
			default:
				return false;
		}

	}
	/**
	* Make the request to licensing api
	*
	* @param string $endpoint The target api endpoint path
	* @param string $query The query string
	* @return object
	* @since    1.0.0
	*/
	private function do_request($args)
	{


		$endpoint = $args['endpoint'] ?: false;
		$query = $args['query'] ?: false;
		$request = isset($args['request']) ? $args['request'] : 'GET';
		$subdomain = isset($args['subdomain']) ? $this->options['ml__api_subdomain'] . '.': '';

		if(!$endpoint || !$query) {
			return "Sorry! You need a query string and an api endpoint";
		}


		$key = isset($args['subdomain']) ? $this->options['ml__subdomain_api_key'] : $this->options['ml__api_key'];
		$base_url = $this->options['ml__api_base_url'];
		$url = "http://{$subdomain}{$base_url}/{$endpoint}?{$query}";

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $request,
			CURLOPT_HTTPHEADER => array(
				"Cache-Control: no-cache",
				"Content-Type: application/json",
				"api-token: $key"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return $err;
		} else {
			return json_decode($response);
		}
	}

	/**
	 * Gets User data from licenceHub
	 */
	public function hub_get_user()
	{
		$query = http_build_query([
			'email' => $this->get_user('email')
		]);
		$raw_data = $this->do_request(array
			(
				'endpoint' => 'Licensing/getUserFromLicenceHub',
				'query' => $query
			)
		);
		if( empty($raw_data->hubuser) ){
			return false;
		} else {
			// add licence hub id to wordpres user meta
			$this->set_user_meta('licenceuserid', $raw_data->hubuser[0]->id);
			return $raw_data->hubuser[0];
		}
	}


	/**
	 * Adds User to LicenceHub
	 */
	public function hub_create_user()
	{
		$uuid_prefix = "mlt_";
		$params= array(
			'email' => $this->get_user('email'),
			'firstname' => $this->get_user('firstname'),
			'lastname' => $this->get_user('lastname'),
			'userid' => $uuid_prefix.$this->get_user('id'),
			'source' => $this->options['ml__api_source']
		);
		$query = http_build_query( $params );

		$raw_data = $this->do_request(array(
			'endpoint' => 'Licensing/addUserToLicenceHub',
			'query' => $query,
			'request' => 'POST'
			)
		);
		return $raw_data;
	}

		/**
	 * Get all licenses from licensing hub by user id
	 * @param string $user_id user id from licensing hub
	 * @param integer $order_id woocomerce order id
	 */
	public function hub_get_licences($user_id)
	{
		$query = http_build_query(
			array(
				'subdomain' => $this->options['ml__api_subdomain'],
				'userid' => $user_id,
			)
		);
		$raw_data = $this->do_request(
			array(
				'endpoint' => 'Licensing/getLicences',
				'query' => $query,
				)
			);
		return $raw_data->licences;
	}
		/**
	 * Adds courses to licensing hub
	 * @param string $user_id user id from licensing hub
	 * @param integer $order_id woocomerce order id
	 */
	public function hub_add_licences($user_id, $order_id)
	{
		$order = wc_get_order( $order_id );
		$items= $order->get_items();
		$sku;
		$queries = array();
		$results = array();

		foreach($items as $item){
			$product = $item->get_product();
			$sku = $product->get_sku();
			$params= array(
				'subdomain' => $this->options['ml__api_subdomain'],
				'userid' => $user_id,
				'sku' => $sku,
				'life' => '31536000',
				'ordernumber' => $order_id,
				'quantity' => $item->get_quantity()
			);
			$query = http_build_query( $params );
			$raw_data = $this->do_request(
				array(
					'endpoint' => 'Licensing/addLicences',
					'query' => $query,
					'request' => 'POST'
					)
				);
			$results[$product->id] = $raw_data;
			}
		return $results;
	}

	/**
	 * Validate user account with one-time authentication into the
	 * LMS from the username and password of a user account
	 */
	public function subdomain_validate_user_account($name, $pass)
	{
		return '0YH5H19SVACKQ6COYAXA';
		$user_name = isset($name) ? $name : $false;
		$user_pass = isset($pass) ? $pass : $false;
		if(!$user_name || !$user_pass){
			return ['error', 'message' => 'Sorry! There was an error. Our admins boffins have been notified'];
		}
		$query = http_build_query([
			'username' => $user_name,
			'password' => $user_pass
		]);
		$raw_data = $this->do_request(
			array(
				'endpoint' => 'User/validateAccount',
				'query' => $query,
				'subdomain' => true
			)
		);
		// The data currently returned is not json notify ML devs
		// return $raw_data;
	}

	/**
	 * Create a new LMS Account
	 * If A user is successfully created this will try to link the account
	 * by calling $this->link_user_accounts()
	 */
	public function subdomain_create_user_account()
	{

		$user_pass = $this->generate_password();
		$params= array(
			'email' => $this->get_user('email'),
			'firstname' => $this->get_user('firstname'),
			'lastname' => $this->get_user('lastname'),
			'username' => $this->get_user('login'),
			'password' => $this->generate_password(),
			'welcomeemail' => 1
		);
		$query = http_build_query($params);
		$raw_data = $this->do_request(
			array(
				'endpoint' => 'User/createUser',
				'query' => $query,
				'subdomain' => true,
				'request' => 'POST'
			)
		);
		if($raw_data->user){
			$this->set_user_meta('lmsuserid', $raw_data->user->id);
			return $this->link_user_accounts() ? true : false;
		} else {
			return false;
		}
	}

	/**
	 * Check if wordpress account is linked in license hub
	 *
	 * @return boolean
	 */
	public function hub_is_account_linked()
	{
		$query = http_build_query(
			array(
				'subdomain' => $this->options['ml__api_subdomain'],
				'licenceuserid' => $this->get_user('licenceuserid')
			)
		);
		$raw_data = $this->do_request(
			array(
				'endpoint' => 'Licensing/getLinkedAccount',
				'query' => $query
			)
		);
		return empty($raw_data->linkeduser) ? false : true;
	}

	/**
	 * Link Wordpress and Blue LMS accounts
	 *
	 * @return boolean
	 */
	public function link_user_accounts()
	{
		$query = http_build_query(
			array(
				'subdomain' => $this->options['ml__api_subdomain'] ,
				'licenceuserid' => $this->get_user('licenceuserid'),
				'lmsuserid' => $this->get_user('lmsuserid')
			)
		);
		$raw_data = $this->do_request(
			array(
				'endpoint' => 'Licensing/addLinkedAccount',
				'query' => $query,
				'request' => 'POST',
			)
		);
		return empty($raw_data->linkeduser) ? false : true;
	}

	/**
	 * Check if wordpress email is in LMS training
	 *
	 * @return boolean
	 */
	public function subdomain_contains_wp_email()
	{
		$query = http_build_query(
			array(
				'search' => $this->get_user('email')
			)
		);
		$raw_data = $this->do_request(
			array(
				'endpoint' => 'User/getUserDetails',
				'query' => $query,
				'subdomain' => true
			)
		);
		// If is single add subdomain id to wp user meta
		if($raw_data->user) { $this->set_user_meta('lmsuserid', $raw_data->user->id); }
		return empty($raw_data->user) ? false : true;
	}
	/**
	 * Check if wordpress email is in LMS training
	 *
	 * @return boolean
	 */
	public function lms_validate()
	{
		$query = http_build_query(
			array(
				'search' => $this->get_user('email')
			)
		);
		$raw_data = $this->do_request(
			array(
				'endpoint' => 'User/getUserDetails',
				'query' => $query,
				'subdomain' => true
			)
		);
		return empty($raw_data->user) ? false : true;
	}


	private function generate_password()
	{
			$length = 16;
			$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			$str = '';
			$max = mb_strlen($keyspace, '8bit') - 1;
			if ($max < 1) {
					throw new Exception('$keyspace must be at least two characters long');
			}
			for ($i = 0; $i < $length; ++$i) {
					$str .= $keyspace[random_int(0, $max)];
			}
			return $str;
	}

}
