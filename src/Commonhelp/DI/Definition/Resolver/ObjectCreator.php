<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\Proxy\ProxyFactory;
use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Definition\ObjectDefinition;
use Commonhelp\DI\Definition\ObjectDefinition\PropertyInjection;
use Commonhelp\DI\Definition\Exception\DefinitionException;
use Commonhelp\DI\Definition\Exception\Commonhelp\DI\Definition\Exception;
use Commonhelp\DI\Definition\Helper\DefinitionHelperInterface;
use Commonhelp\DI\Exception\DependencyException;

class ObjectCreator implements DefinitionResolverInterface{
	
	/**
	 * 
	 * @var ProxyFactory
	 */
	private $proxyFactory;
	
	/**
	 * @var ParameterResolver
	 */
	private $parameterResolver;
	
	/**
	 * 
	 * @var DefinitionResolverInterface
	 */
	private $definitionResolver;
	
	public function __construct(DefinitionResolverInterface $definitionResolver, ProxyFactory $proxyFactory){
		$this->definitionResolver = $definitionResolver;
		$this->proxyFactory = $proxyFactory;
		$this->parameterResolver = new ParameterResolver($definitionResolver);
	}
	
	public function resolve(DefinitionInterface $definition, array $parameters = array()){
		if($definition->isLazy()){
			return $this->createProxy($definition, $parameters);
		}
		
		return $this->createInstance($definition, $parameters);
	}
	
	public function isResolvable(DefinitionInterface $definition, array $parameters = array()){
		return $definition->isInstantiable();
	}
	
	private function createProxy(ObjectDefinition $definition, array $parameters){
		$proxy = $this->proxyFactory->createProxy(
			$definition->getClassName(),
			function(& $wrappedObject, $proxy, $method, $params, &$initializer) use ($definition, $parameters){
				$wrappedObject = $this->createInstance($definition, $parameters);
				$initializer = null;
				
				return true;
			}
		);
		
		return $proxy;
	}
	
	private function createInstance(ObjectDefinition $definition, array $parameters){
		$this->assertClassExists($definition);
		
		$className = $definition->getClassName();
		$classReflection = new \ReflectionClass($className);
		$this->assertClassIsInstantiable($definition);
		
		$constructorInjection = $definition->getConstructorInjection();
		
		try{
			$args = $this->parameterResolver->resolveParameters(
				$constructorInjection,
				$classReflection->getConstructor(),
				$parameters
			);
			
			if(count($args) > 0){
				$object = $classReflection->newInstanceArgs($args);
			}else{
				$object = new $className;
			}
			
			$this->injectMethodsAndProperties($object, $definition);
			
		}catch(\Exception $e){
			throw $e;
		}
		
		if(!$object){
			throw new DependencyException(sprintf(
				'Entry "%s" cannot be resolved: %s could not be constructed',
				$definition->getName(),
				$classReflection->getName()
			));
		}
		
		return $object;
	}
	
	protected function injectMethodsAndProperties($object, ObjectDefinition $objectDefinition){
		foreach($objectDefinition->getPropertyInjections() as $propertyInjection){
			$this->injectProperty($object, $propertyInjection);
		}
		
		foreach($objectDefinition->getMethodInjections() as $methodInjection){
			$methodReflection = new \ReflectionMethod($object, $methodInjection->getMethodName());
			$args = $this->parameterResolver->resolveParameters($methodInjection, $methodReflection);
			
			$methodReflection->invokeArgs($object, $args);
		}
	}
	
	private function injectProperty($object, PropertyInjection $propertyInjection){
		$propertyName = $propertyInjection->getPropertyName();
		
		$className = $propertyInjection->getClassName();
		$className = $className ?: get_class($object);
		$property = new \ReflectionProperty($className, $propertyName);
		
		$value = $propertyInjection->getValue();
		
		if($value instanceof DefinitionHelperInterface){
			$nestedDefinition = $value->getDefinition('');
			
			
			try{
				$value = $this->definitionResolver->resolve($nestedDefinition);
			}catch(DependencyException $e){
				throw $e;
			}catch(\Exception $e){
				throw new DependencyException(sprintf(
					'Error while injecting in %s::%s. %s',
					get_class($object),
					$propertyName,
					$e->getMessage()
				));
			}
		}
		
		if(!$property->isPublic()){
			$property->setAccessible(true);
		}
		
		$property->setValue($object, $value);
	}
	
	private function assertClassExists(ObjectDefinition $definition){
		if(!$definition->classExists()){
			throw new DefinitionException(sprintf(
				'Entry "%s" cannot be resolved: the class doesn\'t exists',
				$definition->getName()
			));
		}
	}
	
	private function assertClassIsInstantiable(ObjectDefinition $definition){
		if(!$definition->isInstantiable()){
			throw new DefinitionException(sprintf(
				'Entry "%s" cannot be resolved: the class is not instantiable',
				$definition->getName()
			));
		}
	}
	
}