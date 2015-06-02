<?php namespace Lti\Sitemap\Test\Datatype;

use \DOMDocument;
use \DOMXPath;

class DOM {

	private $content;
	/**
	 * @var DOMDocument
	 */
	private $dom;

	private $baseXPath = "/html/head/";

	public function __construct($content){
		$this->content = $content;
		$this->dom = $this->makeDOM($content);
	}

	public function makeDOM($content){
		$dom = new DOMDocument();
		@$dom->loadXML($content);
		return $dom;
	}

	public function has($query){
		$xpath = new DOMXPath($this->dom);
		$tags = $xpath->query($this->baseXPath.$query)->length;

		if($tags>0){
			return true;
		}
		return false;
	}

	public function get($query){
		$xpath = new DOMXPath($this->dom);
		return $xpath->query($this->baseXPath.$query);
	}

	public function hasTagWithContent($node,$attribute,$attributeName,$content, $contentName){
		$xpath = new DOMXPath($this->dom);
		$tags = $xpath->query(sprintf('%s%s[@%s="%s"][@%s="%s"]',$this->baseXPath,$node,$attribute,$attributeName,$content, $contentName))->length;

		if($tags>0){
			return true;
		}
		return false;
	}

	public function hasTag($node,$attribute,$attributeName,$content){
		$xpath = new DOMXPath($this->dom);
		$tags = $xpath->query(sprintf('%s%s[@%s="%s"][@%s]',$this->baseXPath,$node,$attribute,$attributeName,$content))->length;

		if($tags>0){
			return true;
		}
		return false;
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

	public function count(){
		$xpath = new DOMXPath($this->dom);
		return $xpath->query('/html/head/*')->length;
	}


}