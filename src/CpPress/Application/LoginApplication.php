<?php
namespace CpPress\Application;

use CpPress\CpPress;
use CpPress\Application\WP\Hook\LoginHook;
use CpPress\Application\WP\Hook\LoginFilter;

class LoginApplication extends CpPressApplication{
	
	
	
	private static $formResult = array();
	
	public function __construct($urlParams=array()){
		parent::__construct(
			'LoginApp', 
			$urlParams,
			array(
					'main' => array(
							'root' 	=> dirname(dirname(dirname(CpPress::$FILE))),
							'uri'	=> plugins_url('', dirname(dirname(CpPress::$FILE)))
					),
			)
		);
		$container = $this->getContainer();
		$container->registerService('LoginHook', function($c){
			return new LoginHook($this);
		});
		$container->registerService('LoginFilter', function($c){
			return new LoginFilter($this);
		});
	}	
	
	public function registerFilters(){
		$filter = $this->getContainer()->query('LoginFilter');
		$filter->massRegister();
		$filter->execAll();
	}
	
	public function registerHooks(){
		$hook = $this->getContainer()->query('LoginHook');
		$hook->massRegister();
		$hook->execAll();
	}
	
	public function registerHook($hook, \Closure $closure, $priority=10, $acceptedArgs=1){
		
	}
	
	
	public function registerFilter($filter, \Closure $closure, $priority=10, $acceptedArgs=1){
		
	}
	
	public function execFilters(){
		$filter = $this->getContainer()->query('LoginFilter');
		$filter->execAll();
	}
	
	public function execHooks(){
		$hook = $this->getContainer()->query('LoginHook');
		$hook->execAll();
	}
}