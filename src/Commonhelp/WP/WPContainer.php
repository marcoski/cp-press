<?php
namespace Commonhelp\WP;

use Commonhelp\App\ApplicationContainer;
use Commonhelp\App\Http\Output;
class WPContainer extends ApplicationContainer{
	
	private $wpThemeRoot;
	private $wpChildRoot;
	
	public function __construct($appName, $urlParams=array(), $wpThemeRoot, $wpChildRoot){
		parent::__construct($appName, $urlParams);
		$this->wpThemeRoot = $wpThemeRoot;
		$this->wpChildRoot = $wpChildRoot;
		$this->registerService('Output', function($c){
			return new Output($this->wpWebRoot);
		});
		$this->registerService('WPDispatcher', function(WPContainer $c){
			return new WPDispatcher(
				$c['Protocol'],
				$c['MiddlewareDispatcher'],
				$c['ControllerMethodAnnotations'],
				$c['Request']
			);
		});
	}
	
	public function getWPThemeRoot(){
		return $this->wpThemeRoot;
	}
	
	public function getWpChildRoot(){
		return $this->wpChildRoot;
	}
	
}