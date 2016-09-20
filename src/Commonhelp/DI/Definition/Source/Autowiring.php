<?php
namespace Commonhelp\DI\Definition\Source;

use Commonhelp\DI\Definition\ObjectDefinition;
use Commonhelp\DI\Definition\ObjectDefinition\MethodInjection;
use Commonhelp\DI\Definition\EntryReference;

class Autowiring implements DefinitionSourceInterface{
	
	public function getDefinition($name){
		if(!class_exists($name) && !interface_exists($name)){
			return null;
		}
		
		$definition = new ObjectDefinition($name);
		
		$class = new \ReflectionClass($name);
		$constructor = $class->getConstructor();
		if($constructor && $constructor->isPublic()){
			$definition->setConstructorInjection(
				MethodInjection::contructor($this->getParamaterDefinition($constructor))
			);
		}
		
		return $definition;
	}
	
	private function getParamaterDefinition(\ReflectionFunctionAbstract $constructor){
		$parameters = array();
		foreach($constructor->getParameters() as $index => $parameter){
			if($parameter->isOptional()){
				continue;
			}
			
			$parameterClass = $parameter->getClass();
			if($parameterClass){
				$parameters[$index] = new EntryReference($parameterClass->getName());
			}
		}
		
		return $parameters;
	}
	
}