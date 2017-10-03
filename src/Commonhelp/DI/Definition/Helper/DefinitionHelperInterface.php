<?php
namespace Commonhelp\DI\Definition\Helper;

use Commonhelp\DI\Definition\DefinitionInterface;

interface DefinitionHelperInterface{
	
	/**
	 * @param string $entryName
	 * @return DefinitionInterface
	 */
	public function getDefinition($entryName);
	
}