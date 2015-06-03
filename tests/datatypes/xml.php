<?php namespace Lti\Sitemap\Test\Datatype;

use \DOMDocument;
use \DOMXPath;

class XML {

	private $content;
	/**
	 * @var DOMDocument
	 */
	private $dom;

	public function __construct($content){
		$this->content = $content;
		$this->dom = $this->makeDOM($content);
	}

	public function makeDOM($content){
		$dom = new DOMDocument();
		@$dom->loadXML($content);
		return $dom;
	}


	public function query($query){
		$xp = new DOMXPath($this->dom);
		$xp->registerNamespace('sitemapindex',"http://www.sitemaps.org/schemas/sitemap/0.9");
		$xp->registerNamespace('sitemap',"http://www.sitemaps.org/schemas/sitemap/0.9");
		$xp->registerNamespace('urlset',"http://www.sitemaps.org/schemas/sitemap/0.9");
		$xp->registerNamespace('image',"http://www.google.com/schemas/sitemap-image/1.1");
		$xp->registerNamespace('news',"http://www.google.com/schemas/sitemap-news/0.9");
		return $xp->query($query);
	}

	/**
	 * @return string
	 */
	public function out() {
		echo "\n\n";
		print_r($this->content);
		echo "\n\n";
	}

	public function has_no_output(){
		return (empty($this->content)||is_null($this->content));
	}

	/**
	 * @param string $content
	 */
	public function set( $content ) {
		$this->content = $content;
	}

}