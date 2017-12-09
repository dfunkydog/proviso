<?php

/**
 * Raw request to licensing api.
 *
 * @link       nlsltd.com
 * @since      1.0.0
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/public/includes
 */

/**
 * Raw request to licensing api.
 *
 * makes the requests to licensing api
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/public/includes
 * @author     Michael Dyer <devteam@nlsltd.com>
 */
class Ml_license_Management {
	/**
	* Make the request to licensing api
	*
	* @param string $endpoint The target api endpoint path, does not expect
	* the ful url
	* @param string $query The query string
	*
	* @since    1.0.0
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

			default:
				return null;
				break;
		}
	}

	private function do_request($args)
	{
		$options = get_option( 'ml__settings' );

		$endpoint = $args['endpoint'] ?: false;
		$query = $args['query'] ?: false;
		$request = $args['request'] ?: GET;
		$subdomain = $args['subdomain'] ? $options['ml__api_subdomain'] . '.': '';

		if(!$endpoint || !$query) {
			return "Sorry! You need a query string and an api endpoint";
		}


		$key = $args['subdomain'] ? $options['ml__subdomain_api_key'] : $options['ml__api_key'];
		$base_url = $options['ml__api_base_url'];
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

	public function hub_get_licenses()
	{

	}


}
