<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Definition\Exception\DefinitionException;

interface DefinitionResolverInterface{
	
	/**
	 * Resolve a definition to a value.
	 *
	 * @param DefinitionInterface $definition Object that defines how the value should be obtained.
	 * @param array      $parameters Optional parameters to use to build the entry.
	 *
	 * @throws DefinitionException If the definition cannot be resolved.
	 *
	 * @return mixed Value obtained from the definition.
	 */
	public function resolve(DefinitionInterface $definition, array $parameters = array());
	/**
	 * Check if a definition can be resolved.
	 *
	 * @param DefinitionInterface $definition Object that defines how the value should be obtained.
	 * @param array      $parameters Optional parameters to use to build the entry.
	 *
	 * @return bool
	*/
	public function isResolvable(DefinitionInterface $definition, array $parameters = array());
}