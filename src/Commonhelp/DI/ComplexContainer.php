<?php
namespace Commonhelp\DI;

use Commonhelp\DI\Exception\InvalidArgumentException;
use Commonhelp\DI\Exception\DependencyException;
use Commonhelp\DI\Exception\FactoryNotFoundException;
use Commonhelp\DI\Definition\Source\DefinitionSourceInterface;
use Commonhelp\DI\Definition\Resolver\DefinitionResolverInterface;
use Commonhelp\DI\Proxy\ProxyFactory;
use Commonhelp\DI\Definition\Resolver\ResolverDispatcher;
use Commonhelp\DI\Exception\Commonhelp\DI\Exception;
use Commonhelp\DI\Exception\QueryException;
use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Definition\Helper\DefinitionHelperInterface;
use Commonhelp\DI\Definition\ObjectDefinition;
use Commonhelp\DI\Definition\InstanceDefinition;
use Commonhelp\DI\Definition\FactoryDefinition;
use Commonhelp\DI\Definition\Source\CachedDefinitionSource;
use Commonhelp\DI\Definition\Source\MutableDefinitionSourceInterface;
use Commonhelp\DI\Invoker\InvokerInterface;
use Commonhelp\DI\Invoker\Invoker;
use Commonhelp\DI\Invoker\ParameterResolver\ResolveChain;
use Commonhelp\DI\Definition\Invoker\DefinitionParameterResolver;
use Commonhelp\DI\Invoker\ParameterResolver\NumericArrayResolver;
use Commonhelp\DI\Invoker\ParameterResolver\AssociativeArrayResolver;
use Commonhelp\DI\Invoker\ParameterResolver\DefaultValueResolver;

class ComplexContainer implements ContainerInterface, FactoryInterface, InvokerInterface{
	
	private $singletonEntries = array();
	
	/**
	 * @var DefinitionSourceInterface
	 */
	private $definitionSource;
	
	/**
	 * @var DefinitionResolverInterface
	 */
	private $definitionResolver;
	
	private $entriesBeingResolved = array();
	
	/**
	 * @var Invoker | null
	 */
	private $invoker;
	
	public function __construct(DefinitionSourceInterface $definitionSource, ProxyFactory $proxyFactory){
		$this->definitionSource = $definitionSource;
		$this->definitionResolver = new ResolverDispatcher($this, $proxyFactory);
		
		$this->singletonEntries['Commonhelp\DI\ComplexContainer'] = $this;
		$this->singletonEntries['Commonhelp\DI\ContainerInterface'] = $this;
		$this->singletonEntries['Commonhelp\DI\FactoryInterface'] = $this;
		$this->singletonEntries['Commonhelp\DI\Invoker\InvokerInterface'] = $this;
	}
	
	/**
	 * Returns an entry of the container by its name.
	 *
	 * @param string $name Entry name or a class name.
	 *
	 * @throws InvalidArgumentException The name parameter must be of type string.
	 * @throws DependencyException Error while resolving the entry.
	 * @throws QueryException No entry found for the given name.
	 * @return mixed
	 */
	public function get($id){
		$name = $id;
		if(!is_string($name)){
			throw new InvalidArgumentException(sprintf(
				'The name parameter must be of type string, %s given',
				is_object($name) ? get_class($name) : gettype($name)
			));
		}
		
		if(array_key_exists($name, $this->singletonEntries)){
			return $this->singletonEntries[$name];
		}
		$definition = $this->definitionSource->getDefinition($name);
		if(!$definition){
			throw new QueryException('No entry or class found ' . $name);
		}
		
		$value = $this->resolveDefinition($definition);
		
		if($definition->getScope() === Scope::SINGLETON){
			$this->singletonEntries[$name] = $value;
		}
		
		return $value;
		
	}
	
	/**
	 * Build an entry of the container by its name.
	 *
	 * This method behave like get() except it forces the scope to "prototype",
	 * which means the definition of the entry will be re-evaluated each time.
	 * For example, if the entry is a class, then a new instance will be created each time.
	 *
	 * This method makes the container behave like a factory.
	 *
	 * @param string $name       Entry name or a class name.
	 * @param array  $parameters Optional parameters to use to build the entry. Use this to force specific parameters
	 *                           to specific values. Parameters not defined in this array will be resolved using
	 *                           the container.
	 *
	 * @throws InvalidArgumentException The name parameter must be of type string.
	 * @throws DependencyException Error while resolving the entry.
	 * @throws FactoryNotFoundException No entry found for the given name.
	 * @return mixed
	 */
	public function make($name, array $parameters = []){
		if(!is_string($name)){
			throw InvalidArgumentException(sprintf(
				'The name parameter must be of type string, %s given',
				is_object($name) ? get_class($name) : gettype($name)
			));
		}
		
		$definition = $this->definitionSource->getDefinition($name);
		if(!$definition){
			if(array_key_exists($name, $this->singletonEntries)){
				return $this->singletonEntries[$name];
			}
			
			throw QueryException("No entry or class found for {$name}");
		}
		
		return $this->resolveDefinition($definition, $parameters);
	}
	
	/**
	 * Test if the container can provide something for the given name.
	 *
	 * @param string $id Entry name or a class name.
	 *
	 * @throws InvalidArgumentException The name parameter must be of type string.
	 * @return bool
	 */
	public function has($id){
		$name = $id;
		if(!is_string($name)){
			throw InvalidArgumentException(sprintf(
				'The name parameter must be of type string, %s given',
				is_object($name) ? get_class($name) : gettype($name)
			));
		}
		
		if(array_key_exists($name, $this->singletonEntries)){
			return true;
		}
		
		$definition = $this->definitionSource->getDefinition($name);
		if($definition === null){
			return false;
		}
		
		return $this->definitionResolver->isResolvable($definition);
	}
	
	/**
	 * Inject all dependencies on an existing instance.
	 *
	 * @param object $instance Object to perform injection upon
	 * @throws InvalidArgumentException
	 * @throws DependencyException Error while injecting dependencies
	 * @return object $instance Returns the same instance
	 */
	public function injectOn($instance){
		$objectDefinition = $this->definitionSource->getDefinition(get_class($instance));
		
		if(!$definition instanceof ObjectDefinition){
			return $instance;
		}
		
		$definition = new InstanceDefinition($instance, $objectDefinition);
		$this->definitionResolver->resolve($definition);
		
		return $instance;
	}
	
	/**
	 * Call the given function using the given parameters.
	 *
	 * Missing parameters will be resolved from the container.
	 *
	 * @param callable $callable   Function to call.
	 * @param array    $parameters Parameters to use. Can be indexed by the parameter names
	 *                             or not indexed (same order as the parameters).
	 *                             The array can also contain DI definitions, e.g. DI\get().
	 *
	 * @return mixed Result of the function.
	 */
	public function call($callable, array $parameters = array()){
		return $this->getInvoker()->call($callable, $parameters);
	}
	
	/**
	 * Define an object or a value in the container.
	 *
	 * @param string                 $name  Entry name
	 * @param DefinitionHelperInterface|mixed $value Value, use definition helpers to define objects
	 */
	public function set($name, $value){
		if($value instanceof DefinitionHelperInterface){
			$value = $value->getDefinition($name);
		}else if($value instanceof \Closure){
			$value = new FactoryDefinition($name, $value);
		}
		
		if($value instanceof DefinitionInterface){
			$this->setDefinition($name, $value);
		}else{
			$this->singletonEntries[$name] = $value;
		}
	}
	
	/**
	 * Registers a service provider.
	 *
	 * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
	 * @param array                    $values   An array of values that customizes the provider
	 *
	 * @return static
	 */
	public function register(ServiceProviderInterface $provider, array $values = array()){
		$provider->register($this);
		foreach ($values as $key => $value) {
			$this->singletonEntries[$key] = $value;
		}
		return $this;
	}
	
	private function resolveDefinition(DefinitionInterface $definition, array $parameters = array()){
		$entryName = $definition->getName();
		
		if(isset($this->entriesBeingResolved[$entryName])){
			throw new DependencyException("Circular dependency detected trying to resolve entry {$entryName}");
		}
		$this->entriesBeingResolved[$entryName] = true;
		
		try{
			$value = $this->definitionResolver->resolve($definition, $parameters);
		}catch(\Exception $e){
			unset($this->entriesBeingResolved[$entryName]);
			throw $e;
		}
		
		unset($this->entriesBeingResolved[$entryName]);
		
		return $value;
	}
	
	private function setDefinition($name, DefinitionInterface $definition){
		if($this->definitionSource instanceof CachedDefinitionSource){
			throw new \LogicException("You cannot set a definition at runtime on container that ha a cache configured.");
		}
		
		if(!$this->definitionSource instanceof  MutableDefinitionSourceInterface){
			throw new \LogicException("The container has not been inistalized correctly");
		}
		
		if(array_key_exists($name, $this->singletonEntries)){
			unset($this->singletonEntries[$name]);
		}
		
		$this->definitionSource->addDefinition($definition);
	}
	
	private function getInvoker(){
		if(!$this->invoker){
			$parameterResolver = new ResolveChain(array(
					new DefinitionParameterResolver($this->definitionResolver),
					new NumericArrayResolver,
					new AssociativeArrayResolver,
					new DefaultValueResolver
			));
			$this->invoker = new Invoker($parameterResolver, $this);
		}
		
		return $this->invoker;
	}
	
}