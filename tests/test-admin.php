<?php namespace Lti\Sitemap\Test;

class AdminTest extends LTI_Sitemap_UnitTestCase {

	private $nonce;

	public function setUp() {
		parent::setUp();
		$this->admin = $this->instance->get_admin();
		$this->nonce = wp_create_nonce( 'lti_sitemap_options' );
	}

	public function testGeneral() {
		$this->assertInstanceOf( 'Lti\Sitemap\Helpers\Wordpress_Helper', $this->instance->get_helper() );
		$this->assertInstanceOf( 'Lti\Sitemap\Admin', $this->instance->get_admin() );
	}

	public function testValidateInputNoToken() {
		$this->admin->validate_input( array( 'lti_sitemap_token' => null ), null );
		$this->assertEquals( 'opt.msg.error_token', $this->admin->get_message() );
	}

	public function testValidateInputWithToken() {
		$this->admin->validate_input( array( 'lti_sitemap_token' => $this->nonce ), null );
		$this->assertEquals( 'opt.msg.update_ok', $this->admin->get_message() );
	}

	public function testValidateInputBadExtraURL() {
		$data = array( 'lti_sitemap_token' => $this->nonce, 'extra_pages_url' => array( 'http/dfkhgefgkhghkfrg.url' ) );

		$this->admin->validate_input( $data, null );
		$this->assertEmpty( $this->admin->get_setting( 'extra_pages_url' ) );
	}

	public function testValidateInputGoodExtraURL() {
		$data = array( 'lti_sitemap_token' => $this->nonce, 'extra_pages_url' => array( 'http://www.example.url' ) );

		$this->admin->validate_input( $data, null );
		$this->assertEquals( $this->admin->get_setting( 'extra_pages_url' ), array( 'http://www.example.url' ) );
	}

	public function testValidateInputBadExtraDate() {
		$data = array(
			'lti_sitemap_token' => $this->nonce,
			'extra_pages_url'   => array( 'http://www.example.url' ),
			'extra_pages_date'  => array( "2150-4" )
		);

		$this->admin->validate_input( $data, null );
		$date = $this->admin->get_setting( 'extra_pages_date' );
		$this->assertNull( $date[0] );
	}

	public function testValidateInputGoodExtraDate() {
		$data = array(
			'lti_sitemap_token' => $this->nonce,
			'extra_pages_url'   => array( 'http://www.example.url' ),
			'extra_pages_date'  => array( '2000-01-01' )
		);

		$this->admin->validate_input( $data, null );
		$this->assertEquals( $this->admin->get_setting( 'extra_pages_date' ), array( '2000-01-01' ) );
	}

	public function testValidateInputContent(){
		$data = array(
			'lti_sitemap_token' => $this->nonce,
			'content_frontpage'=>'on',
			'content_authors'=>true,
			'change_frequency_frontpage'=>'monthly',
			'priority_frontpage'=>'0.1'
		);

		$this->admin->validate_input( $data, null );
		$this->assertEquals( $this->admin->get_setting( 'content_frontpage' ), true);
		$this->assertEquals( $this->admin->get_setting( 'content_authors' ), true);
		$this->assertEquals( $this->admin->get_setting( 'change_frequency_frontpage' ),'monthly' );
		$this->assertEquals( $this->admin->get_setting( 'change_frequency_posts' ),'weekly' );
		$this->assertEquals( $this->admin->get_setting( 'priority_frontpage' ), '0.1' );
	}


}