<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\Definition\DefinitionInterface;

class ValueResolver implements DefinitionResolverInterface{
	
	public function resolve(DefinitionInterface $definition, array $parameters = array()){
		return $definition->getValue();
	}
	
	public function isResolvable(DefinitionInterface $definition, array $parameters = array()){
		return true;
	}
	
}