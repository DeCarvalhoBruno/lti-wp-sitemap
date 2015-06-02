<?php namespace Lti\Sitemap\Test;

class SettingsTest extends LTI_Sitemap_UnitTestCase {

	public function testSettings() {
		$settings = $this->instance->get_settings();
		$this->assertInstanceOf( "Lti\\Sitemap\\Plugin\\Plugin_Settings", $this->instance->get_settings() );

		foreach ( $settings as $setting ) {
			$this->assertInstanceOf( "Lti\\Sitemap\\Plugin\\Fields", $setting );
		}
	}

	/**
	 *
	 * @covers \Lti\Sitemap\Plugin\Plugin_Settings::get
	 * @covers \Lti\Sitemap\Plugin\Plugin_Settings::set
	 */
	public function testGetSet(){
		$settings = $this->instance->get_settings();
		$settings->set('key','test_value','Text');
		$this->assertEquals('test_value',$settings->get('key'));
	}

	/**
	 *
	 * @covers \Lti\Sitemap\Plugin\Plugin_Settings::set
	 */
	public function testCheckbox(){
		$settings = $this->instance->get_settings();
		$settings->set('chk1',true,'Checkbox');
		$settings->set('chk2',false,'Checkbox');
		$settings->set('chk3',"string",'Checkbox');
		$settings->set('chk4',"true",'Checkbox');
		$settings->set('chk5',"false",'Checkbox');

		$this->assertEquals(true,$settings->get('chk1'));
		$this->assertEquals(false,$settings->get('chk2'));
		$this->assertEquals(false,$settings->get('chk3'));
		$this->assertEquals(true,$settings->get('chk4'));
		$this->assertEquals(false,$settings->get('chk5'));
	}



}

