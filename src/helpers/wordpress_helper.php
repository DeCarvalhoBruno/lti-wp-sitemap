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

	public function home_url(){
		return home_url('/');
	}

	public function sitemap_url(){
		return $this->home_url()."sitemap.xml";
	}


	public static function get_supported_post_types() {
		/**
		 * Allow filtering of supported post types
		 *
		 * @api array the list of supported types
		 */
		return apply_filters('lti_supported_post_types',get_post_types( array( 'public' => true, 'show_ui' => true ) ));
	}

}