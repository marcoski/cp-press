<?php
namespace Commonhelp\DI\Definition\ObjectDefinition;

use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Scope;

class MethodInjection implements DefinitionInterface{
	
	private $methodName;
	
	private $parameters = array();
	
	public function __construct($methodName, array $parameters = array()){
		$this->methodName = (string) $methodName;
		$this->parameters = $parameters;
	}
	
	public static function contructor(array $parameters = array()){
		return new self('__construct', $parameters);
	}
	
	public function getMethodName(){
		return $this->methodName;
	}
	
	public function getParameters(){
		return $this->parameters;
	}
	
	public function replaceParameters(array $parameters){
		$this->parameters = $parameters;
	}
	
	public function merge(MethodInjection $definition){
		$this->parameters = array_merge($this->parameters, $definition->getParameters());
	}
	
	public function getName(){
		return null;
	}
	
	public function getScope(){
		return Scope::PROTOTYPE;
	}
	
}