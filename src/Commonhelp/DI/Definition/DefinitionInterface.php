<?php
namespace Commonhelp\DI\Definition;

use Commonhelp\DI\Factory\RequestedEntryInterface;

interface DefinitionInterface extends RequestedEntryInterface{
	
	/**
	 * @see \Commonhelp\DI\Factory\RequestedEntryInterface::getName()
	 */
	public function getName();
	
	/**
	 * Returns the scope of the entry
	 * 
	 * @return string
	 */
	public function getScope();
	
}