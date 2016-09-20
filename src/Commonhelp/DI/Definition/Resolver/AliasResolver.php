<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\ComplexContainer;
use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Definition\AliasDefinition;

class AliasResolver implements DefinitionResolverInterface{
	
	/**
	 * 
	 * @var ComplexContainer
	 */
	private $container;
	
	public function __construct(ComplexContainer $container){
		$this->container = $container;
	}
	
	/**
	 * @param AliasDefinition $definition;
	 */
	public function resolve(DefinitionInterface $definition, array $parameters = array()){
		return $this->container->get($definition->getTargetEntryName());
	}
	
	/**
	 * @param AliasDefinition $definition;
	 */
	public function isResolvable(DefinitionInterface $definition, array $parameters = array()){
		return $this->container->has($definition->getTargetEntryName());
	}
}