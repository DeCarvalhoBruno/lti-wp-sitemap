<?php namespace Lti\Sitemap;

use Lti\Sitemap\Generators\Sitemap_Generator_Authors;
use Lti\Sitemap\Generators\Sitemap_Generator_Index;
use Lti\Sitemap\Generators\Sitemap_Generator_Main;
use Lti\Sitemap\Generators\Sitemap_Generator_Pages;
use Lti\Sitemap\Generators\Sitemap_Generator_Posts;
use Lti\Sitemap\Generators\Sitemap_Generator_Posts_Month;
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

	/**
	 * @param string $plugin_name
	 * @param string $version
	 * @param Plugin_Settings $settings
	 * @param ICanHelp $helper
	 */
	public function __construct(
		$plugin_name,
		$version,
		Plugin_Settings $settings,
		ICanHelp $helper
	) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->settings    = $settings;
		$this->helper      = $helper;
	}

	public function build_sitemap( $type = null, $month = null, $year = null ) {
		switch ( $type ) {
			case 'main':
				$sitemap = new Sitemap_Generator_Main( $this->settings, $this->helper );
				break;
			case 'posts':
				if ( ! is_null( $month ) ) {
					$this->settings->set( 'month', $month );
					$this->settings->set( 'year', $year );
				}
				$sitemap = new Sitemap_Generator_Posts( $this->settings, $this->helper );
				break;
			case 'pages':
				$sitemap = new Sitemap_Generator_Pages( $this->settings, $this->helper );
				break;
			case 'authors':
				$sitemap = new Sitemap_Generator_Authors( $this->settings, $this->helper );
				break;
			default:
				$sitemap = new Sitemap_Generator_Index( $this->settings, $this->helper );
		}

		//echo get_option('permalink_structure');


		return $sitemap->get();

	}

}
