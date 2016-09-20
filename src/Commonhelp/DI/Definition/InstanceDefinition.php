<?php
namespace Commonhelp\DI\Definition;

use Commonhelp\DI\Scope;
class InstanceDefinition implements DefinitionInterface{
	
	private $instance;
	
	/**
	 * @var ObjectDefinition
	 */
	private $objectDefinition;
	
	public function __construct($instance, ObjectDefinition $objectDefinition){
		$this->instance = $instance;
		$this->objectDefinition = $objectDefinition;
	}
	
	public function getName(){
		return '';
	}
	
	public function getScope(){
		return Scope::PROTOTYPE;
	}
	
	public function getInstance(){
		return $this->instance;
	}
	
	/**
	 * @return ObjectDefinition
	 */
	public function getObjectDefinition(){
		return $this->objectDefinition;
	}
	
}