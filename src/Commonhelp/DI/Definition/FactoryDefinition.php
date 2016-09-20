<?php
namespace Commonhelp\DI\Definition;

use Commonhelp\DI\Scope;
class FactoryDefinition implements DefinitionInterface{
	
	private $name;
	
	private $scope;
	
	/**
	 * @var callable
	 */
	private $factory;
	
	public function __construct($name, $factory, $scope=null){
		$this->name = $name;
		$this->factory = $factory;
		$this->scope = $scope;
	} 
	
	public function getName(){
		return $this->name;
	}
	
	public function getScope(){
		return $this->scope ?: Scope::SINGLETON;
	}
	
	/**
	 * @return callable
	 */
	public function getCallable(){
		return $this->factory;
	}
}