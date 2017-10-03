<?php
namespace Commonhelp\Rss;

use Commonhelp\Client\Client;
use Commonhelp\Rss\Reader\Reader;

class RssReaderTest extends \PHPUnit_Framework_TestCase{
	
	private $feedUrl = 'http://www.dinamopress.it/?format=feed&type=rss';
	private $siteUrl = 'http://www.dinamopress.it';
	
	public function testRead(){
		$reader = new Reader();
		$resource = $reader->download($this->feedUrl);
		$parser = $reader->getParser(
				$resource->getUrl(),
				$resource->getContent(),
				$resource->getEncoding()
		);
		
		//$parser->enableContentGrabber();
		
		// Return a Feed object
		$feed = $parser->execute();
		//print_r($feed);
		$this->assertNotEmpty($feed);
	}
	
	public function testDiscover(){
		$reader = new Reader;
		$resource = $reader->download($this->siteUrl);
		
		$feeds = $reader->find(
				$resource->getUrl(),
				$resource->getContent()
		);
		
		$this->assertNotEmpty($feeds);
	}
	
}