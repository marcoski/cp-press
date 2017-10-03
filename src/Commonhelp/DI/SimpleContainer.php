<?php
namespace Commonhelp\DI;

use Closure;
use ReflectionClass;
use Commonhelp\DI\IContainer;
use Commonhelp\DI\Exception\QueryException;
use Commonhelp\DI\Annotations\AnnotationsBuilder;
use Commonhelp\DI\Annotations\TypeAnnotations;

class SimpleContainer extends Container {
	
	/**
	 * @param ReflectionClass $class the class to instantiate
	 * @return \stdClass the created class
	 */
	private function buildClass(ReflectionClass $class){
		$constructor = $class->getConstructor();
		$annotations = new AnnotationsBuilder($class);
		if($constructor === null){
			return $class->newInstance();
		}else{
			$parameters = [];
			foreach($constructor->getParameters() as $parameter){
				try{
					$parameterClass = $parameter->getClass();
					// try to find out if it is a class or a simple parameter
					if ($parameterClass === null) {
						$resolveName = $parameter->getName();
					} else {
						$resolveName = $parameterClass->name;
					}
					
					$parameters[] = $this->query($resolveName);
				}catch(QueryException $ex){
					
					$resolveName = $annotations->get('Type', function(TypeAnnotations $annotations) use($constructor, $parameter){
						return $annotations->getType($parameter->getName(), $constructor->getName());
					});
					$parameters[] = $this->query($resolveName);
				}
			}
			return $class->newInstanceArgs($parameters);
		}
	}
	
	/**
	 * If a parameter is not registered in the container try to instantiate it
	 * by using reflection to find out how to build the class
	 * @param string $name the class name to resolve
	 * @return \stdClass
	 * @throws QueryException if the class could not be found or instantiated
	 */
	public function resolve($name) {
		$baseMsg = 'Could not resolve ' . $name . '!';
		try {
			$class = new ReflectionClass($name);
			if ($class->isInstantiable()) {
				return $this->buildClass($class);
			} else {
				throw new QueryException($baseMsg .
						' Class can not be instantiated');
			}
		} catch(\ReflectionException $e) {
			throw new QueryException($baseMsg . ' ' . $e->getMessage());
		}
	}
	
	/**
	 * @param string $name name of the service to query for
	 * @return mixed registered service for the given $name
	 * @throws QueryException if the query could not be resolved
	 */
	public function query($name) {
		$name = $this->sanitizeName($name);
		if ($this->offsetExists($name)) {
			return $this->offsetGet($name);
		} else {
			$object = $this->resolve($name);
			$this->registerService($name, function () use ($object) {
				return $object;
			});
			return $object;
		}
	}
	
	public function querySet($pattern, $exclude=array()){
		$objects = array();
		foreach($this->keys() as $name){
			if(preg_match('/'.$pattern.'/', $name) && !in_array($name, $exclude)){
				$objects[$name] = $this->query($name);
			}
		}
		
		return $objects;
	}
	
	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function registerParameter($name, $value) {
		$this[$name] = $value;
	}
	
	/**
	 * The given closure is call the first time the given service is queried.
	 * The closure has to return the instance for the given service.
	 * Created instance will be cached in case $shared is true.
	 *
	 * @param string $name name of the service to register another backend for
	 * @param Closure $closure the closure to be called on service creation
	 * @param bool $shared
	 */
	public function registerService($name, Closure $closure, $shared = true) {
		$name = $this->sanitizeName($name);
		if (isset($this[$name]))  {
			unset($this[$name]);
		}
		if ($shared) {
			$this[$name] = $closure;
		} else {
			$this[$name] = parent::factory($closure);
		}
	}
	
	/**
	 * Shortcut for returning a service from a service under a different key,
	 * e.g. to tell the container to return a class when queried for an
	 * interface
	 * @param string $alias the alias that should be registered
	 * @param string $target the target that should be resolved instead
	 */
	public function registerAlias($alias, $target) {
		$this->registerService($alias, function (ContainerInterface $container) use ($target) {
			return $container->query($target);
		}, false);
	}
	/*
	 * @param string $name
	 * @return string
	 */
	protected function sanitizeName($name) {
		return ltrim($name, '\\');
	}
}