<?php namespace Lti\Sitemap\Test;

use Lti\Sitemap\LTI_Sitemap;

class Lti_Sitemap_UnitTestCase extends \WP_UnitTestCase {

	/**
	 * @var Lti_Sitemap
	 */
	protected $instance;
	/**
	 * @var \Lti\Sitemap\Admin
	 */
	protected $admin;

	protected $output;

	public function setUp() {
		parent::setUp();
		$this->instance = Lti_Sitemap::get_instance();
	}

	public function go_to( $url ) {
		$_GET = $_POST = array();
		foreach (
			array(
				'query_string',
				'id',
				'postdata',
				'authordata',
				'day',
				'currentmonth',
				'page',
				'pages',
				'multipage',
				'more',
				'numpages',
				'pagenow'
			) as $v
		) {
			if ( isset( $GLOBALS[ $v ] ) ) {
				unset( $GLOBALS[ $v ] );
			}
		}
		$parts = parse_url( $url );
		if ( isset( $parts['scheme'] ) ) {
			$req = isset( $parts['path'] ) ? $parts['path'] : '';
			if ( isset( $parts['query'] ) ) {
				$req .= '?' . $parts['query'];
				parse_str( $parts['query'], $_GET );
			}
		} else {
			$req = $url;
		}
		if ( ! isset( $parts['query'] ) ) {
			$parts['query'] = '';
		}

		$_SERVER['REQUEST_URI'] = $req;
		unset( $_SERVER['PATH_INFO'] );

		$this->flush_cache();
		unset( $GLOBALS['wp_query'], $GLOBALS['wp_the_query'] );
		$GLOBALS['wp_the_query'] = new \WP_Query();
		$GLOBALS['wp_query']     = $GLOBALS['wp_the_query'];
		$GLOBALS['wp']           = new \WP();
		_cleanup_query_vars();

		$GLOBALS['wp']->main( $parts['query'] );
	}

	public function display_error( $error ) {
		return sprintf( "\x1b[30;43m%s\x1b[0m", $error );
	}

	public function is_date( $date, $format = 'Y-m-d\TH:i:sP' ) {
		$d = \DateTime::createFromFormat( $format, $date );

		return $d && $d->format( $format ) == $date;
	}

}

