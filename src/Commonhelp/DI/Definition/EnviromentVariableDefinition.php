<?php
namespace Commonhelp\DI\Definition;

use Commonhelp\DI\Scope;
class EnviromentVariableDefinition implements CachableDefinitionInterface{
	
	private $name;
	
	private $variableName;
	
	private $isOptional;
	
	private $defaultValue;
	
	private $scope;
	
	public function __construct($name, $variableName, $isOptional = false, $defaultValue = null){
		$this->name = $name;
		$this->variableName = $variableName;
		$this->isOptional = $isOptional;
		$this->defaultValue = $defaultValue;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getVariableName(){
		return $this->variableName;
	}
	
	public function isOptional(){
		return $this->isOptional;
	}
	
	public function getDefaultValue(){
		return $this->defaultValue;
	}
	
	public function setScope($scope){
		$this->scope = $scope;
	}
	
	public function getScope(){
		return $this->scope ?: Scope::SINGLETON;
	}
	
}