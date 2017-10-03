<?php
namespace Commonhelp\DI\Definition\ObjectDefinition;

class PropertyInjection{
	
	private $propertyName;
	
	private $value;
	
	private $className;
	
	public function __construct($propertyName, $value, $className = null){
		$this->propertyName = $propertyName;
		$this->value = $value;
		$this->className = $className;
	}
	
	public function getPropertyName(){
		return $this->propertyName;
	}
	
	public function getValue(){
		return $this->value;
	}
	
	public function getClassName(){
		return $this->className;
	}
	
}