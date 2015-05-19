<?php namespace Lti\Sitemap\Helpers;

interface ICanHelp {
}

/**
 * Does anything wordpress related on behalf of generators
 *
 * Class Wordpress_Helper
 * @package Lti\Sitemap\Helpers
 */
class Wordpress_Helper implements ICanHelp {

	/**
	 * @var \Lti\Sitemap\Plugin\Plugin_Settings
	 */
	protected $settings;

	public function __construct( $settings ) {
		$this->settings = $settings;
	}

	public function get( $value ) {
		return $this->settings->get( $value );
	}

	public function get_settings() {
		return $this->settings;
	}

	public function filter_var_array($data, $filter = FILTER_SANITIZE_STRING){
		return filter_var_array($data, $filter);
	}

}