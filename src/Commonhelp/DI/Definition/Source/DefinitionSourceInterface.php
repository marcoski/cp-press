<?php
namespace Commonhelp\DI\Definition\Source;

use Commonhelp\DI\Definition\Exception\DefinitionException;
use Commonhelp\DI\Definition\DefinitionInterface;

interface DefinitionSourceInterface{
	
	/**
	 * Returns the DI definition for the entry name.
	 *
	 * @param string $name
	 *
	 * @throws DefinitionException An invalid definition was found.
	 * @return DefinitionInterface|null
	 */
	public function getDefinition($name);
}