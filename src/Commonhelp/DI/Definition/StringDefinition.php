<?php
namespace Commonhelp\DI\Definition;

use Commonhelp\DI\Scope;
class StringDefinition implements DefinitionInterface{
	
	private $name;
	
	private $expression;
	
	public function __construct($name, $expression){
		$this->name = $name;
		$this->expression = $expression;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getScope(){
		return Scope::SINGLETON;
	}
	
	public function getExpression(){
		return $this->expression;
	}
	
}