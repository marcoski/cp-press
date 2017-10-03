<?php
namespace Commonhelp\DI\Definition\Helper;

use Commonhelp\DI\Definition\EnviromentVariableDefinition;

class EnviromentVariableDefinitionHelper implements DefinitionHelperInterface{
	
	private $variableName;
	
	private $isOptional;
	
	private $defaultValue;
	
	public function __construct($variableName, $isOptional, $defaultValue = null){
		$this->variableName = $variableName;
		$this->isOptional = $isOptional;
		$this->defaultValue = $defaultValue;
	}
	
	public function getDefinition($entryName){
		return new EnviromentVariableDefinition($entryName, $this->variableName, $this->isOptional, $this->defaultValue);
	}
	
}