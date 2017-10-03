<?php
namespace Commonhelp\DI\Definition;

use Commonhelp\DI\Scope;
class ValueDefinition implements DefinitionInterface{
	
	private $name;
	
	private $value;
	
	public function __construct($name, $value){
		$this->name = $name;
		$this->value = $value;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getScope(){
		return Scope::SINGLETON;
	}
	
	public function getValue(){
		return $this->value;
	}
	
}