<?php
namespace Commonhelp\DI\Definition;

class ArrayDefinitionExtension extends ArrayDefinition implements SubDefinitionInterface{
	
	/**
	 * @var ArrayDefinition
	 */
	private $subDefinition;
	
	public function getValues(){
		if(!$this->subDefinition){
			return parent::getValues();
		}
		
		return array_merge($this->subDefinition->getValues(), parent::getValues());
	}
	
	public function getSubDefinitionName(){
		return $this->getName();
	}
	
	public function setSubDefinition(DefinitionInterface $definition){
		if(!$definition instanceof ArrayDefinition){
			throw new DefinitionException(sprintf(
					'Definition %s tries to add array entries but the previous definition is not an array',
					$this->getName()
			));
		}
		
		$this->subDefinition = $definition;
	}
	
}