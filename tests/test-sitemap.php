<?php namespace Lti\Sitemap\Test;

class SitemapTest extends LTI_Sitemap_UnitTestCase {

	/**
	 * @var \Lti\Sitemap\Frontend;
	 */
	private $frontend;

	public function setUp() {
		parent::setUp();
		$this->admin = $this->instance->get_admin();
		$this->frontend = $this->instance->get_frontend();
	}

	public function testSitemapInit(){
		$XMLString = $this->frontend->build_sitemap();

		//$this->assertNotEmpty($XMLString);
		$this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>',$XMLString);

		$xml = new \DOMDocument();

		//echo $this->frontend->build_sitemap('index');

		$xml->loadXML($XMLString);

//		$xp = new \DOMXPath($xml);
//		$result = $xp->query('/sitemapindex');
//		//$result = $xml->getElementsByTagName('sitemapindex');
//		echo "\n\n\n\n";
//		echo $result->length;







//
//		$q = new DOMXPath($x);
//		$q->registerNamespace('sitemapindex',"http://www.sitemaps.org/schemas/sitemap/0.9");
//		$q->registerNamespace('sitemap',"http://www.sitemaps.org/schemas/sitemap/0.9");
//		$result = $q->query('//sitemapindex:sitemap/sitemap:loc');










	}

/*
 <?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="http://example.org/wp-content/plugins/mnt/hgfs/www/ldb/app/plugins/lti-sitemap/assets/dist/xsl/sitemap.xsl"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc><![CDATA[http://example.org/sitemap-main.xml]]></loc>
  </sitemap>
</sitemapindex>

 */

}

