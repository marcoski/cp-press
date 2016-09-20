<?php
namespace Commonhelp\Util\Annotations;

abstract class AbstractAnnotations implements AnnotationsInterface, \ArrayAccess{
	
	protected $methods;
	
	protected $properties;
	
	protected $constructor;
	
	protected $annotations = array();
	
	/**
	 * 
	 * @var \ReflectionClass $reflector
	 */
	protected $reflector;
	
	public function init($object){
		if($object instanceof \ReflectionClass){
			$this->reflector = $object;
		}else{
			$this->reflector = new \ReflectionClass($object);
		}
		foreach($this->reflector->getMethods(\ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PUBLIC) as $method){
			$this->methods[$method->getName()] = $method->getDocComment();
		}
		foreach($this->reflector->getProperties(\ReflectionProperty::IS_STATIC | \ReflectionProperty::IS_PUBLIC) as $property){
			$this->properties[$property->getName()] = $property->getDocComment();
		}
		$this->constructor = $this->reflector->getConstructor() === null ? null : $this->reflector->getConstructor()->getDocComment();
	}
	
	public function get($name){
		if($name instanceof \Reflector){
			return $this->getByReflector($name);
		}else if(is_string($name)){
			if(isset($this->methods[$name])){
				return $this->methods[$name];
			}
			if(isset($this->properties[$name])){
				return $this->properties[$name];
			}
			if($name === '__constructor' || $name === 'constructor'){
				return $this->constructor;
			}
		}
		
		return null;
	}
	
	protected function getByReflector(\Reflector $name){
		if(method_exists($name, 'isConstructor') && $name->isConstructor()){
			return $this->get('__constructor');
		}
		if(method_exists($name, 'getName')){
			return $this->get($name->getName());
		}
		
		return null;
	}
	
	public function has($name){
		if($name instanceof \Reflector){
			return $this->hasByReflector($name);
		}else if(is_string($name)){
			if(isset($this->methods[$name])){
				return true;
			}
			if(isset($this->properties[$name])){
				return true;
			}
			if($name === '__constructor' || $name === 'constructor'){
				return true;
			}
		}
		
		return false;
	}
	
	protected function hasByReflector(\Reflector $name){
		if(method_exists($name, 'isConstructor') && $name->isConstructor()){
			return $this->has('__constructor');
		}
		if(method_exists($name, 'getName')){
			return $this->has($name->getName());
		}
		
		return false;
	}
	
	public function offsetGet($name){
		return $this->get($name);
	}
	
	public function offsetExists($name){
		return $this->has($name);
	}
	
	public function offsetSet($offset, $value){
		throw new \RuntimeException('Can\'t set annotations');
	}
	
	public function offsetUnset($offset){
		throw new \RuntimeException('Can\'t unset annotations');
	}
	
	abstract public function parse();
	
	
	
}