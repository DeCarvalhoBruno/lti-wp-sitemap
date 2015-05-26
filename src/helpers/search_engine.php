<?php namespace Lti\Sitemap\Helpers;

abstract class Search_Engine_Helper {

	public static function http_request($url){
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_exec( $ch );
		$result = curl_getinfo( $ch );
		curl_close( $ch );
		return $result;
	}

}