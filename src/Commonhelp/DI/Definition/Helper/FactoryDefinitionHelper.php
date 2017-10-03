<?php
namespace Commonhelp\DI\Definition\Helper;

use Commonhelp\DI\Definition\DecoratorDefinition;
use Commonhelp\DI\Definition\FactoryDefinition;

class FactoryDefinitionHelper implements DefinitionHelperInterface{
	
	private $factory;
	
	private $scope;
	
	private $decorate;
	
	public function __construct($factory, $decorate = false){
		$this->factory = $factory;
		$this->decorate = $decorate;
	}
	
	/**
	 * 
	 * @param bool $scope
	 * @return FactoryDefinitionHelper
	 */
	public function scope($scope){
		$this->scope = $scope;
		
		return $this;
	}
	
	public function getDefinition($entryName){
		if($this->decorate){
			return new DecoratorDefinition($entryName, $this->factory, $this->scope);
		}
		
		return new FactoryDefinition($entryName, $this->factory, $this->scope);
	}
	
}