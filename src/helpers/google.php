<?php namespace Lti\Sitemap\Helpers;

use Google_Client;
use Google_Service_Webmasters;


class Google_Helper {

	/**
	 * @var Google_Client
	 */
	private $client;
	/**
	 * @var Google_Service_Webmasters
	 */
	private $service_webmaster;

	private $access_token;

	private $is_authenticated;

	public function __construct() {
		$this->client = $this->initialize_google_client();
	}

	private function initialize_google_client() {
		$client = new Google_Client();
		$client->setClientId( '384177546309-l0qgbfi3v9695nd0tonpu95310qhkmgf.apps.googleusercontent.com' );
		$client->setClientSecret( 'wjHfFujvQauLYUvNzLYi-ZO1' );
		$client->setScopes( array( 'https://www.googleapis.com/auth/webmasters' ) );
		$client->setRedirectUri( 'urn:ietf:wg:oauth:2.0:oob' );
		$client->setAccessType( 'offline' );
		$client->setDeveloperKey( 'AIzaSyAApoI_39_L7J1PXseCRD27NM0gQozmrzA' );
		$client->setApplicationName( 'LTI Sitemap' );

		return $client;
	}

	public function initialize_services() {
		$this->service_webmaster = new Google_Service_Webmasters( $this->client );
	}

	public function get_authentication_url() {
		//Hardening our client a bit by setting a per request state
		$this->client->setState(mt_rand());
		return $this->client->createAuthUrl();
	}

	public function set_access_token( $access_token ) {
		$this->access_token = $access_token;
		$this->client->setAccessToken( $access_token );
	}

	public function get_access_token() {
		return $this->access_token;
	}

	public function authenticate( $authentication_key ) {
		$this->client->authenticate( $authentication_key );
		$this->set_access_token($this->client->getAccessToken());
		$this->is_authenticated = true;
		return $this->access_token;
	}

	public function is_authenticated(){
		return ($this->is_authenticated===true);
	}
}

//
//class Google_Webmaster{
//
//	private $client;
//
//	public function __construct(Google_Client $client){
//		$this->client = new Google_Service_Webmasters($client);
//	}
//
//
//}