<?php namespace Lti\Sitemap\Test;

use Lti\Sitemap\Plugin\Postbox_Values;
use Lti\Sitemap\Test\Datatype\XML;
use Lti\Sitemap\XMLSitemap;

class SitemapTest extends LTI_Sitemap_UnitTestCase {

	/**
	 * @var \Lti\Sitemap\Frontend;
	 */
	private $frontend;

	public function setUp() {
		parent::setUp();
		$this->admin    = $this->instance->get_admin();
		$this->frontend = $this->instance->get_frontend();

	}

	public function testSitemapIndex() {
		$userID = $this->factory->user->create();
		$this->factory->post->create( array( 'post_author' => $userID ) );

		$this->frontend->set_setting( 'content_pages', true );
		$this->frontend->set_setting( 'content_authors', true );
		$this->frontend->set_setting( 'content_posts', true );

		$XMLString = $this->frontend->build_sitemap();

		$this->assertStringStartsWith( '<?xml version="1.0" encoding="UTF-8"?>', $XMLString );

		$xml    = new XML( $XMLString );
		$result = $xml->query( '//sitemapindex:sitemap' );
		$this->assertEquals( 3, $result->length,
			"The sitemap index is supposed to contain a page, author and post sitemap" );
	}

	public function testSitemapPosts() {
		XMLSitemap::reset();
		$userID = $this->factory->user->create();
		$this->factory->post->create( array( 'post_author' => $userID ) );
		$this->factory->post->create( array( 'post_author' => $userID ) );
		$this->factory->post->create( array( 'post_author' => $userID ) );

		$XMLString = $this->frontend->build_sitemap( 'posts' );

		$xml    = new XML( $XMLString );
		$result = $xml->query( '//urlset:url' );

		$this->assertEquals( 3, $result->length,
			$this->display_error( "The posts sitemap is supposed to have 3 posts" ) );
		$this->assertTrue( filter_var( $result->item( 0 )->childNodes->item( 1 )->nodeValue,
				FILTER_VALIDATE_URL ) !== false );
		$this->assertEquals( $result->item( 0 )->childNodes->item( 3 )->nodeName, 'lastmod' );
		$this->assertTrue( $this->is_date( $result->item( 0 )->childNodes->item( 3 )->nodeValue ) );
		$this->assertEquals( $result->item( 0 )->childNodes->item( 5 )->nodeName, 'changefreq' );
		$this->assertEquals( $result->item( 0 )->childNodes->item( 5 )->nodeValue,
			$this->admin->get_setting( 'change_frequency_posts' ) );
		$this->assertEquals( $result->item( 0 )->childNodes->item( 7 )->nodeName, 'priority' );
		$this->assertEquals( $result->item( 0 )->childNodes->item( 7 )->nodeValue,
			$this->admin->get_setting( 'priority_posts' ) );
	}

	public function testSitemapPages(){
		XMLSitemap::reset();

		$this->factory->post->create(array('post_type'=>'page'));
		$XMLString = $this->frontend->build_sitemap( 'pages' );
		$xml       = new XML( $XMLString );
		$result    = $xml->query( '//urlset:url' );

		$this->assertTrue($result->length==1);
		$this->assertTrue($result->item(0)->childNodes->length>0);
	}

	public function testSitemapAuthors(){
		XMLSitemap::reset();
		$userID = $this->factory->user->create();
		$this->factory->post->create( array( 'post_author' => $userID ) );

		$XMLString = $this->frontend->build_sitemap( 'authors' );
		$xml       = new XML( $XMLString );
		$result    = $xml->query( '//urlset:url' );

		$this->assertTrue($result->length==1);
		$this->assertTrue($result->item(0)->childNodes->length>0);
	}

	public function testSitemapPostsWithImage() {
		XMLSitemap::reset();
		$license = 'http://creativecommons.org/licenses/by/4.0/';
		$userID  = $this->factory->user->create();
		$postID  = $this->factory->post->create( array( 'post_author' => $userID ) );
		$this->factory->attachment->create_object( 'post.png', $postID,
			array( 'post_mime_type' => 'image/jpeg', 'post_type' => 'attachment', 'post_content' => $license ) );

		$this->frontend->set_setting( 'content_images_support', true );

		$XMLString = $this->frontend->build_sitemap( 'posts' );
		$xml       = new XML( $XMLString );
		$result    = $xml->query( '//urlset:url/image:image/*' );

		$this->assertEquals( $result->item( 0 )->nodeName, 'image:loc' );
		$this->assertContains( 'post.png', $result->item( 0 )->nodeValue );

		$this->assertEquals( $result->item( 1 )->nodeName, 'image:license' );
		$this->assertEquals( $result->item( 1 )->nodeValue, $license );
	}

	public function testSitemapNews() {
		XMLSitemap::reset();
		$keywords = 'One,two,three';
		$stock_tickers = 'STOCK1:VALUE1,KCOTS:EULAV';
		$postID = $this->factory->post->create();
		$this->frontend->set_setting('news_publication','PUBLICATION');
		update_post_meta( $postID, 'lti_sitemap_post_is_news', true );
		update_post_meta( $postID, 'lti_sitemap', new Postbox_Values( (object) array(
			'news_access_type'   => 'Subscription',
			'news_genre_blog'    => true,
			'news_keywords'      => $keywords,
			'news_stock_tickers' => $stock_tickers
		) ) );

		$this->frontend->set_setting( 'content_news_support', true );

		$XMLString = $this->frontend->build_sitemap( 'news' );
		$xml       = new XML( $XMLString );
		$result    = $xml->query( '//urlset:url/news:news/*' );

		$this->assertEquals( $result->item( 0 )->nodeName, 'news:publication' );
		$this->assertEquals( $result->item( 0 )->childNodes->item(1)->nodeName, 'news:name' );
		$this->assertEquals( $result->item( 0 )->childNodes->item(3)->nodeName, 'news:language' );

		$this->assertEquals( $result->item( 1 )->nodeName, 'news:access' );
		$this->assertEquals( $result->item( 1 )->nodeValue, 'Subscription' );

		$this->assertEquals( $result->item( 2 )->nodeName, 'news:genres' );
		$this->assertEquals( $result->item( 2 )->nodeValue, 'Blog' );

		$this->assertEquals( $result->item( 3 )->nodeName, 'news:publication_date' );
		$this->assertTrue( $this->is_date( $result->item( 3 )->nodeValue ) );

		$this->assertEquals( $result->item( 4 )->nodeName, 'news:title' );
		$this->assertContains( 'Post title', $result->item( 4 )->nodeValue );

		$this->assertEquals( $result->item( 5 )->nodeName, 'news:keywords' );
		$this->assertEquals( $result->item( 5 )->nodeValue, $keywords);

		$this->assertEquals( $result->item( 6 )->nodeName, 'news:stock_tickers' );
		$this->assertEquals( $result->item( 6 )->nodeValue, $stock_tickers);
	}

}

