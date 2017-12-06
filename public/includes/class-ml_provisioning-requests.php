<?php

/**
 * Raw request to licensing api.
 *
 * @link       nlsltd.com
 * @since      1.0.0
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/includes
 */

/**
 * Raw request to licensing api.
 *
 * makes the requests to licensing api
 *
 * @package    Ml_provisioning
 * @subpackage Ml_provisioning/includes
 * @author     Michael Dyer <devteam@nlsltd.com>
 */
class Ml_provisioning_Requests {
	/**
	 * Make the request to licensing api
	*
	* @since    1.0.0
	*/

	private function do_request($endpoint)
	{
		$key = get_option( 'ml__settings' );
		$url = "{$endpoint}?api_key={$key['ml__api_key']}";
		//  Initiate curl
		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL, $url);
		// Execute
		$result = curl_exec($ch);
		if (curl_errno($ch))
		{
			throw new Exception(curl_error($ch));
		}
		// Closing
		curl_close($ch);
		$results = json_decode($result, true);

		return $results;
	}

	public function get_movies(){
		$raw_data = $this->do_request("https://api.themoviedb.org/3/movie/550");
		return $raw_data;
	}
}
