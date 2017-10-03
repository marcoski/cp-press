<?php
namespace Commonhelp\DI\Definition;

use Commonhelp\DI\Scope;

class ArrayDefinition implements DefinitionInterface{
	
	private $name;
	
	private $values;
	
	public function __construct($name, array $values){
		$this->name = $name;
		$this->values = $values;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getScope(){
		return Scope::SINGLETON;
	}
	
	public function getValues(){
		return $this->values;
	}
	
}