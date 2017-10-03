<?php
namespace Commonhelp\DI\Definition;

interface SubDefinitionInterface{
	
	/**
	 * @return string
	 */
	public function getSubDefinitionName();
	
	/**
	 * @param DefinitionInterface $definition
	 */
	public function setSubDefinition(DefinitionInterface $definition);
	
}