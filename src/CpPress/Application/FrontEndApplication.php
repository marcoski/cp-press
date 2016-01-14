<?php
namespace CpPress\Application;

use Closure;
use \Commonhelp\WP\WPApplication;
use CpPress\Application\FrontEnd\FrontPageController;
use CpPress\Application\WP\Hook\FrontEndHook;
use CpPress\Application\WP\Hook\Filter;

class FrontEndApplication extends CpPressApplication{
	
	public function __construct($urlParams=array()){
		parent::__construct(
			'FrontEndApp', 
			$urlParams,
			array(
					'main' => array(
							'root' 	=> get_template_directory(),
							'uri'	=> get_template_directory_uri()
					),
					'child' => array(
							'root'	=> get_stylesheet_directory(),
							'uri'	=> get_stylesheet_directory_uri()
					)
			)
		);
		
		$container = $this->getContainer();
		$container->registerService('FrontPageController', function($c){
			return new FrontPageController(
				'', 
				$c->query('Request'),
				array(
					$this->childRoot,
					$this->themeRoot	
				)
			);
		});
		$container->registerService('FrontEndHook', function($c){
			return new FrontEndHook($this);
		});
		$container->registerService('FrontEndFilter', function($c){
			return new Filter($this);
		});
	}
	
	public function setup(){
		global $wp_scripts, $wp_styles;
		//init styles and scripts global
		$wp_styles = $this->getStyles();
		$wp_scripts = $this->getScripts();
	}
	
	public function registerHooks(){
		$hook = $this->getContainer()->query('FrontEndHook');
		$hook->massRegister();
	}
	
	public function registerHook($hook, Closure $closure, $priority=10, $acceptedArgs=1){
		$hookObj = $this->getContainer()->query('FrontEndHook');
		$hookObj->register($hook, $closure, $priority, $acceptedArgs);
	}
	
	
	public function execHooks(){
		$hook = $this->getContainer()->query('FrontEndHook');
		$hook->execAll();
	}
	
	public function registerFilters(){
		$filter = $this->getContainer()->query('FrontEndFilter');
	
	}
	
	public function registerFilter($filter, Closure $closure, $priority=10, $acceptedArgs=1){
		$filterObj = $this->getContainer()->query('FrontEndFilter');
		$filterkObj->register($filter, $closure, $priority, $acceptedArgs);
	}
	
	public function execFilters(){
		$filter = $this->getContainer()->query('FrontEndFilter');
		$filter->execAll();
	}
	
}