<?php
namespace Commonhelp\App;

use Commonhelp\DI\SimpleContainer;
use Commonhelp\App\Exception\ApplicationException;

abstract class AbstractApplication implements ApplicationInterface{

	protected $container;
	
	public function __construct(){
		$this->container = null;
	}
	
	public function dispatch($controllerName, $methodName){
		if(is_null($this->container)){
			throw new ApplicationException('None container initialized');
		}
		
		self::main($controllerName, $methodName, $this->container);
	}
	
	public function getContainer(){
		return $this->container;
	}

}