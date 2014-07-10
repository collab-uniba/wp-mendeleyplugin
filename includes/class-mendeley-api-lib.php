<?php
/**
 * Created by PhpStorm.
 * User: davide
 * Date: 08/07/14
 * Time: 14:20
 */

if ( ! class_exists( "Client" ) ) {
	include_once 'vendor/autoload.php';
}
define( 'AUTHORIZE_ENDPOINT', 'https://mix.mendeley.com/oauth/authorize' );
define( 'TOKEN_ENDPOINT', 'https://mix.mendeley.com/oauth/token' );
define( 'API_ENDPOINT', 'https://mix.mendeley.com/' );

class MendeleyApi {

	protected $client = null;

	protected $client_id;

	protected $client_secret;

	protected $callback_url;

	function __construct() {
		$this->client_id     = '';
		$this->client_secret = '';
		$this->callback_url  = '';
	}

	public function init(){
		$this->client = new \OAuth2\Client($this->client_id, $this->client_secret);
	}

	/**
	 * @return string
	 */
	public function get_callback_url() {
		return $this->callback_url;
	}

	/**
	 * @param string $callback_url
	 */
	public function set_callback_url( $callback_url ) {
		$this->callback_url = $callback_url;
	}

	/**
	 * @return string
	 */
	public function get_client_id() {
		return $this->client_id;
	}

	/**
	 * @param string $client_id
	 */
	public function set_client_id( $client_id ) {
		$this->client_id = $client_id;
	}

	/**
	 * @return string
	 */
	public function get_client_secret() {
		return $this->client_secret;
	}

	/**
	 * @param string $client_secret
	 */
	public function set_client_secret( $client_secret ) {
		$this->client_secret = $client_secret;
	}


	public function start_authorization_flow() {
		$url = $this->client->getAuthenticationUrl( AUTHORIZE_ENDPOINT, $this->callback_url, array( 'scope' => 'all' ) );
		wp_redirect( $url );
		exit();
	}

	public function get_access_token( $auth_code ) {
		// set request parameters
		$params   = array( 'code' => $auth_code, 'redirect_uri' => $this->callback_url );
		$response = $this->client->getAccessToken( TOKEN_ENDPOINT, 'authorization_code', $params );

		return $response;
	}

	public function refresh_access_token( $refresh_token ) {
		$params   = array( 'refresh_token' => $refresh_token );
		$response = $this->client->getAccessToken( TOKEN_ENDPOINT, 'refresh_token', $params );

		return $response;
	}

	public function get_authored_publications() {
		$url = API_ENDPOINT . 'documents/authored';

		return $this->client->fetch( $url );
	}

	public function get_document( $id ) {
		$url = API_ENDPOINT . 'documents/' . $id;

		return $this->client->fetch( $url );
	}

}