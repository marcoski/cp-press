<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Exception\DependencyException;

class InstanceInjector extends ObjectCreator{
	
	public function resolve(DefinitionInterface $definition, array $parameters = array()){
		try{
			$this->injectMethodsAndProperties($definition->getInstance(), $definition->getObjectDefinition());
		}catch(\Exception $e){
			throw new DependencyException(sprintf(
				'Error while injecting dependencies into %s: %s',
				get_class($definition->getInstance()),
				$e->getMessage()
			));
		}
	}
	
	public function isResolvable(DefinitionInterface $definition, array $parameters = array()){
		return true;
	}
	
}