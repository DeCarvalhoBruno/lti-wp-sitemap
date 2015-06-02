<?php namespace Lti\Sitemap\Test;

class AdminTest extends LTI_Sitemap_UnitTestCase {


	public function setUp() {
		parent::setUp();
	}

	public function testGeneral() {
		$helper = $this->instance->get_helper();
		$this->assertInstanceOf('Lti\Sitemap\Helpers\Wordpress_Helper',$helper);
	}


}