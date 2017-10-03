<?php
namespace Commonhelp\Rss;

use Commonhelp\Rss\Scraper\Scraper;

class RssGrabberTest extends \PHPUnit_Framework_TestCase{
	
	private $url = 'http://www.dinamopress.it/inchieste/poligrow-il-volto-oscuro-del-capitalismo-italiano-in-colombia';
	
	public function testGrab(){
		$config = new RssConfig();
		$grabber = new Scraper($config);
		$grabber->setUrl($this->url);
		$grabber->execute();
		
		print_r($grabber->getRelevantContent());
	}
	
}