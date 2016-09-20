<?php
namespace Commonhelp\DI\Definition;

use Commonhelp\DI\Definition\ObjectDefinition\MethodInjection;
use Commonhelp\DI\Definition\ObjectDefinition\PropertyInjection;
use Commonhelp\DI\Scope;

class ObjectDefinition implements DefinitionInterface, CachableDefinitionInterface, SubDefinitionInterface{
	
	private $name;
	
	private $className;
	
	/** 
	 * @var MethodInjection|null
	 */
	private $constructorInjection;
	
	/**
	 * @var PropertyInjection[]
	 */
	private $propertyInjections = array();
	
	/**
	 * @var MethodInjection[]
	 */
	private $methodInjections = array();
	
	private $scope;
	
	private $lazy;
	
	private $classExtists;
	
	private $isInstantiable;
	
	public function __construct($name, $className = null){
		$this->name = (string) $name;
		$this->setClassName($className);
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setClassName($className){
		$this->className = $className;
		$this->updateCache();
	}
	
	public function getClassName(){
		if($this->className !== null){
			return $this->className;
		}
		
		return $this->name;
	}
	
	/**
	 * @return MethodInjection|null
	 */
	public function getConstructorInjection(){
		return $this->constructorInjection;
	}
	
	/**
	 * @param MethodInjection $constructorInjection
	 */
	public function setConstructorInjection(MethodInjection $constructorInjection){
		$this->constructorInjection = $constructorInjection;
	}
	
	/**
	 * @return PropertyInjection[]
	 */
	public function getPropertyInjections(){
		return $this->propertyInjections;
	}
	
	public function addPropertyInjection(PropertyInjection $propertyInjection){
		$className = $propertyInjection->getClassName();
		if($className){
			$key = $className . '::' . $propertyInjection->getPropertyName();
		}else{
			$key = $propertyInjection->getPropertyName();
		}
		
		$this->propertyInjections[$key] = $propertyInjection;
	}
	
	/**
	 * @return MethodInjection[]
	 */
	public function getMethodInjections(){
		$injections = array();
		array_walk_recursive($this->methodInjections, function($injection) use (&$injections){
			$injections[] = $injection;
		});
		
		return $injections;
	}
	
	/**
	 * @param MethodInjection $methodInjection
	 */
	public function addMethodInjection(MethodInjection $methodInjection){
		$method = $methodInjection->getMethodName();
		if(!isset($this->methodInjections[$method])){
			$this->methodInjections[$method] = array();
		}
		
		$this->methodInjections[$method][] = $methodInjection;
	}
	
	public function setScope($scope){
		$this->scope = $scope;
	}
	
	public function getScope(){
		return $this->scope ?: Scope::SINGLETON;
	}
	
	public function setLazy($lazy){
		$this->lazy = $lazy;
	}
	
	public function getLazy(){
		return $this->lazy;
	}
	
	public function isLazy(){
		if($this->lazy !== null){
			return $this->lazy;
		}else{
			return false;
		}
	}
	
	public function classExists(){
		return $this->classExtists;
	}
	
	public function isInstantiable(){
		return $this->isInstantiable;
	}
	
	public function getSubDefinitionName(){
		return $this->getClassName();
	}
	
	public function setSubDefinition(DefinitionInterface $definition){
		if(!$definition instanceof self){
			return;
		}
		
		if($this->className === null){
			$this->setClassName($definition->getClassName());
		}
		
		if($this->scope === null){
			$this->scope = $definition->getScope();
		}
		
		if($this->lazy === null){
			$this->lazy = $definition->getLazy();
		}
		
		$this->mergeConstructorInjection($definition);
		$this->mergePropertyInjections($definition);
		$this->mergeMethodInjections($definition);
	}
	
	private function mergeConstructorInjection(ObjectDefinition $definition){
		if($definition->getConstructorInjection() !== null){
			if($this->constructorInjection !== null){
				$this->constructorInjection->merge($definition->getConstructorInjection());
			}else{
				$this->constructorInjection = $definition->getConstructorInjection();
			}
		}
	}
	
	private function mergePropertyInjections(ObjectDefinition $definition){
		foreach($definition->getPropertyInjections() as $propertyName => $propertyInjection){
			if(!isset($this->propertyInjections[$propertyName])){
				$this->propertyInjections[$propertyName] = $propertyInjection;
			}
		}
	}
	
	private function mergeMethodInjections(ObjectDefinition $definition){
		foreach($definition->getMethodInjections() as $methodName => $calls){
			
		}
	}
	
	private function mergeMethodCalls(array $calls, $methodName){
		foreach($calls as $index => $methodInjection){
			if(array_key_exists($index, $this->methodInjections[$methodName])){
				$this->methodInjections[$methodName][$index]->merge($methodInjection);
			}else{
				$this->methodInjections[$methodName][$index] = $methodInjection;
			}
		}
	}
	
	private function updateCache(){
		$className = $this->getClassName();
		$this->classExtists = class_exists($className) || interface_exists($className);
		
		if(!$this->classExtists){
			$this->isInstantiable = false;
			return;
		}
		
		$class = new \ReflectionClass($className);
		$this->isInstantiable = $class->isInstantiable();
	}
	
}