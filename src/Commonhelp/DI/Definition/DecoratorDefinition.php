<?php
namespace Commonhelp\DI\Definition;

class DecoratorDefinition extends FactoryDefinition implements DefinitionInterface, SubDefinitionInterface{
	
	/**
	 * @var DefinitionInterface
	 */
	private $decorate;
	
	public function getSubDefinitionName(){
		return $this->getName();
	}
	
	public function setSubDefinition(DefinitionInterface $definition){
		$this->decorate = $definition;
	}
	
	/**
	 * @return DefinitionInterface
	 */
	public function getDecorateDefinition(){
		return $this->decorate;
	}
	
}