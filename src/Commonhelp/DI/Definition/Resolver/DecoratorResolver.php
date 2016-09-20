<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\ComplexContainer;
use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Definition\DecoratorDefinition;

class DecoratorResolver implements DefinitionResolverInterface{
	
	/**
	 * @var ComplexContainer;
	 */
	private $container;
	
	/**
	 * @var DefinitionResolverInterface;
	 */
	private $definitionResolver;
	
	public function __construct(ComplexContainer $container, DefinitionResolverInterface $definitionResolver){
		$this->container = $container;
		$this->definitionResolver = $definitionResolver;
	}
	
	/**
	 * @param DecoratorDefinition $definition
	 */
	public function resolve(DefinitionInterface $definition, array $parameters = array()){
		$callable = $definition->getCallable();
		
		if(!is_callable($callable)){
			throw new DependencyException(sprintf(
				'The decorator "%s" is not callable'.
				$definition->getName()
			));
		}
		
		$decoratedDefinition = $definition->getDecoratedDefinition();
		
		if(!$decoratedDefinition instanceof DefinitionInterface){
			if(!$definition->getSubDefinitionName()){
				throw new DefinitionException('Decorators cannot be nested in another definition');
			}
			
			throw new DefinitionException(sprintf(
				'Entry "%s" decorate nothing: no previous definition with the same name was found',
				$definition->getName()
			));
		}
		
		$decorated = $this->definitionResolver->resolve($decoratedDefinition);
		
		return call_user_func($callable, $decorated, $this->container);
	}
	
	public function isResolvable(DefinitionInterface $definition, array $parameters = array()){
		return true;
	}
	
}