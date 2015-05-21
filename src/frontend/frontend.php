<?php namespace Lti\Sitemap;

use Lti\Sitemap\Helpers\ICanHelp;
use Lti\Sitemap\Plugin\Plugin_Settings;


/**
 * Takes care of displaying sitemaps
 *
 * Class Frontend
 * @package Lti\Sitemap
 */
class Frontend {

	private $plugin_name;
	private $version;

	/**
	 * @var \Lti\Sitemap\Plugin\Plugin_Settings
	 */
	private $settings;

	/**
	 * @var ICanHelp|\Lti\Sitemap\Helpers\Wordpress_Helper
	 */
	private $helper;
	private $plugin_basename;

	/**
	 * @param string $plugin_name
	 * @param string $version
	 * @param Plugin_Settings $settings
	 * @param ICanHelp $helper
	 */
	public function __construct(
		$plugin_name,
		$plugin_path,
		$version,
		Plugin_Settings $settings,
		ICanHelp $helper
	) {

		$this->plugin_name = $plugin_name;
		$this->plugin_path = $plugin_path;
		$this->version     = $version;
		$this->settings    = $settings;
		$this->helper      = $helper;
	}

	public function build_sitemap(){

	}

}
