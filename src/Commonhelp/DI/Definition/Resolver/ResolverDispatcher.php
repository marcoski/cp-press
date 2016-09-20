<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\ComplexContainer;
use Commonhelp\DI\Proxy\ProxyFactory;
use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Definition\ObjectDefinition;
use Commonhelp\DI\Definition\ValueDefinition;
use Commonhelp\DI\Definition\DecoratorDefinition;
use Commonhelp\DI\Definition\AliasDefinition;
use Commonhelp\DI\Definition\FactoryDefinition;
use Commonhelp\DI\Definition\EnviromentVariableDefinition;
use Commonhelp\DI\Definition\StringDefinition;
use Commonhelp\DI\Definition\InstanceDefinition;
use Commonhelp\DI\Definition\ArrayDefinition;

class ResolverDispatcher implements DefinitionResolverInterface{
	
	/**
	 * 
	 * @var ComplexContainer
	 */
	private $container;
	
	/**
	 * 
	 * @var ProxyFactory
	 */
	private $proxyFactory;
	
	private $valueResolver;
	private $arrayResolver;
	private $factoryResolver;
	private $decoratorResolver;
	private $aliasResolver;
	private $objectResolver;
	private $instanceResolver;
	private $envVariableResolver;
	private $stringResolver;
	
	public function __construct(ComplexContainer $container, ProxyFactory $proxyFactory){
		$this->container = $container;
		$this->proxyFactory = $proxyFactory;
	}
	
	public function resolve(DefinitionInterface $definition, array $parameters = array()){
		$definitionResolver = $this->getDefinitionResolver($definition);
		return $definitionResolver->resolve($definition, $parameters);
	}
	
	public function isResolvable(DefinitionInterface $definition, array $parameters = array()){
		$definitionResolver = $this->getDefinitionResolver($definition);
		return $definitionResolver->isResolvable($definition, $parameters);
	}
	
	/**
	 * 
	 * @param DefinitionInterface $definition
	 * @throws \RuntimeException
	 * @return DefinitionResolverInterface
	 */
	private function getDefinitionResolver(DefinitionInterface $definition){
		switch(true){
			case $definition instanceof ObjectDefinition:
				if(!$this->objectResolver){
					$this->objectResolver = new ObjectCreator($this, $this->proxyFactory);
				}
				
				return $this->objectResolver;
			case $definition instanceof ValueDefinition:
				if(!$this->valueResolver){
					$this->valueResolver = new ValueResolver();
				}
				
				return $this->valueResolver;
			case $definition instanceof AliasDefinition:
				if(!$this->aliasResolver){
					$this->aliasResolver = new AliasResolver($this->container);
				}
				
				return $this->aliasResolver;
			case $definition instanceof DecoratorDefinition:
				if(!$this->decoratorResolver){
					$this->decoratorResolver = new DecoratorResolver($this->container, $this);
				}
				
				return $this->decoratorResolver;
			case $definition instanceof FactoryDefinition:
				if(!$this->factoryResolver){
					$this->factoryResolver = new FactoryResolver($this->container);
				}
				
				return $this->factoryResolver;
			case $definition instanceof ArrayDefinition:
				if(!$this->arrayResolver){
					$this->arrayResolver = new ArrayResolver($this);
				}
				
				return $this->arrayResolver;
			case $definition instanceof EnviromentVariableDefinition:
				if(!$this->envVariableResolver){
					$this->envVariableResolver = new EnviromentVariabelResolver($this);
				}
				
				return $this->envVariableResolver;
			case $definition instanceof StringDefinition:
				if(!$this->stringResolver){
					$this->stringResolver = new StringResolver($this->container);
				}
				
				return $this->stringResolver;
			case $definition instanceof InstanceDefinition:
				if(!$this->instanceResolver){
					$this->instanceResolver = new InstanceInjector($this, $this->proxyFactory);
				}
				
				return $this->instanceResolver;
			default:
				throw new \RuntimeException('No definition resolver was configured for definition type ' . get_class($definition));
			
		}
	}
	
}