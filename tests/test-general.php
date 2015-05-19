<?php namespace Lti\Sitemap\Test;

use Lti\Sitemap\Deactivator;
use Lti\Sitemap\Activator;

class GeneralTest extends LTI_Sitemap_UnitTestCase {

	/**
	 *
	 * @covers \Lti\Sitemap\Activator::activate
	 */
	public function testActivation() {
		require_once plugin_dir_path( __FILE__ ) . '../src/activator.php';
		Activator::activate();
		$stored_options = get_option( "lti_sitemap_options" );
		$this->assertInstanceOf( "Lti\\Sitemap\\Plugin\\Plugin_Settings", $stored_options );
	}

	/**
	 *
	 * @covers \Lti\Sitemap\Deactivator::deactivate
	 */
	public function testDeactivation() {
		require_once plugin_dir_path( __FILE__ ) . '../src/deactivator.php';
		Deactivator::deactivate();
	}
}

