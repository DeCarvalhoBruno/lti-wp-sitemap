<?php namespace Lti\Sitemap\Helpers;


class Bing_Helper {


	private $submission_url = "http://www.bing.com/ping?sitemap=%s";
	private $sitemap_url;

	public function __construct($sitemap_url){
		$this->sitemap_url = $sitemap_url;
	}

	public function get_submission_url(){
		return sprintf($this->submission_url,$this->sitemap_url);
	}

}