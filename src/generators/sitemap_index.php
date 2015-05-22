<?php namespace Lti\Sitemap\Generators;

use Lti\Sitemap\Helpers\ICanHelp;
use Lti\Sitemap\Helpers\Plugin_Queries;
use Lti\Sitemap\Plugin\Plugin_Settings;
use Lti\Sitemap\Sitemap;
use Lti\Sitemap\SiteMapIndex;
use Lti\Sitemap\SitemapUrl;
use Lti\Sitemap\SiteMapUrlSet;

interface ICanGenerateSitemaps {
	public function get();
}

abstract class Sitemap_Generator implements ICanGenerateSitemaps {

	/**
	 * @var Plugin_Settings
	 */
	protected $settings;
	/**
	 * @var ICanHelp|\Lti\Sitemap\Helpers\Wordpress_Helper
	 */
	protected $helper;

	protected $filenamePrefix = "sitemap-";

	/**
	 * @param Plugin_Settings $settings
	 * @param ICanHelp|\Lti\Sitemap\Helpers\Wordpress_Helper $helper
	 */
	public function __construct( Plugin_Settings $settings, ICanHelp $helper ) {
		$this->settings = $settings;
		$this->helper   = $helper;
		$this->filename = $helper->home_url() . $this->filenamePrefix;
		$this->query    = new Plugin_Queries();

	}

}

class Sitemap_Generator_Authors extends Sitemap_Generator {
	public function get() {
		$sitemap_pages = new SiteMapUrlSet();
		$sitemap_pages->addStylesheet( plugin_dir_url( __FILE__ ) . 'sitemap.xsl' );
		$result = $this->query->get_authors($this->helper->get_supported_post_types() );

		$changeFrequency = $this->settings->get( 'change_frequency_authors' );
		$priority        = $this->settings->get( 'priority_authors' );

		if ( ! empty( $result ) ) {
			foreach ( $result as $entry ) {
				$sitemap_pages->add( new SitemapUrl( get_author_posts_url( $entry->ID ), lti_iso8601_date( $entry->lastmod ),
					$changeFrequency, $priority ) );
			}
		}
		return $sitemap_pages->output();
	}
}

class Sitemap_Generator_Pages extends Sitemap_Generator {
	public function get() {
		$sitemap_pages = new SiteMapUrlSet();
		$sitemap_pages->addStylesheet( plugin_dir_url( __FILE__ ) . 'sitemap.xsl' );

		$changeFrequency = $this->settings->get( 'change_frequency_pages' );
		$priority        = $this->settings->get( 'priority_pages' );

		$result = $this->query->get_pages();
		if ( ! empty( $result ) ) {
			foreach ( $result as $entry ) {
				$sitemap_pages->add( new SitemapUrl( get_permalink( $entry->ID ), lti_iso8601_date( $entry->lastmod ),
					$changeFrequency, $priority ) );
			}
		}

		return $sitemap_pages->output();
	}
}

class Sitemap_Generator_Main extends Sitemap_Generator {
	public function get() {
		$sitemap_main = new SiteMapUrlSet();
		$sitemap_main->addStylesheet( plugin_dir_url( __FILE__ ) . 'sitemap.xsl' );

		if ( $this->settings->get( 'content_frontpage' ) == true ) {
		$sitemap_main->add( new SitemapUrl( $this->helper->home_url(),
			lti_iso8601_date( get_lastpostmodified( 'gmt' ) ), $this->settings->get( 'change_frequency_frontpage' ),
			$this->settings->get( 'priority_frontpage' ) ) );
		}

		if ( $this->settings->get( 'content_user_defined' ) == true ) {
			$other_pages = $this->settings->get( 'extra_pages_url' );
			if ( ! is_null( $other_pages ) && ! empty( $other_pages ) ) {
				$other_pages_mod_date = $this->settings->get( 'extra_pages_date' );
				$changeFrequency      = $this->settings->get( 'change_frequency_user_defined' );
				$priority             = $this->settings->get( 'priority_user_defined' );

				foreach ( $other_pages as $key => $page ) {
					$sitemap_main->add( new SitemapUrl( $page, lti_iso8601_date( $other_pages_mod_date[ $key ] ),
						$changeFrequency, $priority ) );
				}
			}
		}

		return $sitemap_main->output();
	}

}

class Sitemap_Generator_Index extends Sitemap_Generator {

	public function get() {
		$sitemap_index = new SiteMapIndex();
		$sitemap_index->addStylesheet( plugin_dir_url( __FILE__ ) . 'sitemap.xsl' );
		if ( $this->settings->get( 'content_frontpage' ) == true ) {
			$sitemap_index->add( new Sitemap( $this->filename . 'main.xml',
				lti_iso8601_date( get_lastpostmodified( 'gmt' ) ) ) );
		}

		if ( $this->settings->get( 'content_posts' ) == true ) {
			$result = $this->query->get_dates_with_posts();
			if ( ! empty( $result ) ) {
				foreach ( $result as $entry ) {
					$sitemap_index->add( new Sitemap( $this->filename . sprintf( 'posts-%s-%s.xml',
							$entry->year, str_pad( $entry->month, 2, '0', STR_PAD_LEFT ) ),
						lti_iso8601_date( $entry->lastmod ) ) );
				}
			}
		}

		if ( $this->settings->get( 'content_pages' ) == true ) {
			$result = $this->query->get_pages_info();
			if ( ! empty( $result ) ) {
				$sitemap_index->add( new Sitemap( $this->filename . 'pages.xml',
					lti_iso8601_date( $result[0]->lastmod ) ) );

			}
		}

		if ( $this->settings->get( 'content_authors' ) == true ) {
			$result = $this->query->get_authors_info( $this->helper->get_supported_post_types() );
			if ( ! empty( $result ) ) {
				$sitemap_index->add( new Sitemap( $this->filename . 'authors.xml',
					lti_iso8601_date( $result[0]->lastmod ) ) );

			}
		}

		return $sitemap_index->output();
	}

}

class Sitemap_Generator_Posts extends Sitemap_Generator {

	public function get() {
		$sitemap_posts = new SiteMapUrlSet();
		$sitemap_posts->addStylesheet( plugin_dir_url( __FILE__ ) . 'sitemap.xsl' );

		$month           = $this->settings->get( 'month' );
		$year            = $this->settings->get( 'year' );
		$result          = $this->query->get_posts( $month, $year );
		$changeFrequency = $this->settings->get( 'change_frequency_posts' );
		$priority        = $this->settings->get( 'priority_posts' );


		if ( ! empty( $result ) ) {
			foreach ( $result as $entry ) {
				$sitemap_posts->add( new SitemapUrl( get_permalink( $entry->ID ), lti_iso8601_date( $entry->lastmod ),
					$changeFrequency, $priority ) );
			}
		}

		return $sitemap_posts->output();

	}


}
