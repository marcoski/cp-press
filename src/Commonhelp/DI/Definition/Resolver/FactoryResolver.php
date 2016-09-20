<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\ComplexContainer;
use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Invoker\Invoker;
use Commonhelp\DI\Invoker\ParameterResolver\ResolveChain;
use Commonhelp\DI\Definition\Invoker\FactoryParameterResolver;
use Commonhelp\DI\Invoker\ParameterResolver\NumericArrayResolver;
use Commonhelp\DI\Invoker\Exception\NotEnoughParametersException;
use Commonhelp\DI\Definition\Exception\DefinitionException;

class FactoryResolver implements DefinitionResolverInterface{
	
	/**
	 * 
	 * @var ComplexContainer
	 */
	private $container;
	
	/**
	 * @var Invoker | null
	 */
	private $invoker;
	
	public function __construct(ComplexContainer $container){
		$this->container = $container;
	}
	
	
	public function resolve(DefinitionInterface $definition, array $parameters = array()){
		if(!$this->invoker){
			$parameterResolver = new ResolveChain(array(
					new FactoryParameterResolver($this->container),
					new NumericArrayResolver
			));
			
			$this->invoker = new Invoker($parameterResolver, $this->container);
		}
		
		try{
			return $this->invoker->call($definition->getCallable(), array($this->container, $definition));
		}catch(NotCallableException $e){
			throw new DefinitionException(sprintf(
				'Entry "%s" cannot be rsolved: factory %s',
				$definition->getName(),
				$e->getMessage()
			));
		}catch(NotEnoughParametersException $e){
			throw new DefinitionException(sprintf(
				'Entry "%s" cannot be resolved: %s',
				$definition->getName(),
				$e->getMessage()
			));
		}
	}
	
	public function isResolvable(DefinitionInterface $definition, array $parameters = array()){
		return true;
	}
}