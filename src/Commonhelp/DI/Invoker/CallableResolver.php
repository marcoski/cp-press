<?php
namespace Commonhelp\DI\Invoker;

use Commonhelp\DI\ContainerInterface;
use Commonhelp\DI\Invoker\Exception\NotCallableException;

class CallableResolver{
	
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	public function __construct(ContainerInterface $container){
		$this->container = $container;
	}
	
	public function resolve($callable){
		if(is_string($callable) && strpos($callable, '::') !== false){
			$callable = explode('::', $callable, 2);
		}
		
		$callable = $this->resolveFromContainer($callable);
		if(!is_callable($callable)){
			throw new NotCallableException(sprintf(
				'%s is not a callable',
				is_object($callable) ? 'Instance of ' . get_class($callable) : var_export($callable, true)
			));
		}
		
		return $callable;
	}
	
	private function resolveFromContainer($callable){
		if($callable instanceof \Closure){
			return $callable;
		}
		
		$isStaticCallToNonStaticMethod = false;
		
		if(is_callable($callable)){
			$isStaticCallToNonStaticMethod = $this->isStaticCallToNonStaticMethod($callable);
			if(!$isStaticCallToNonStaticMethod){
				return $callable;
			}
		}
			
		if(is_string($callable)){
			if($this->container->has($callable)){
				return $this->container->get($callable);
			}else{
				throw new NotCallableException(sprintf(
					'"%s" is neither a callable nor a valid container entry',
					$callable
				));
			}
		}
		
		if(is_array($callable) && is_string($callable[0])){
			if($this->container->has($callable[0])){
				$callable[0] = $this->container->get($callable[0]);
				return $callable;
			}else if($isStaticCallToNonStaticMethod){
				throw new NotCallableException(sprintf(
					'Cannot call %s::%s() because %s() is not a static method and "%s" is not a container entry',
					$callable[0],
					$callable[1],
					$callable[1],
					$callable[0]
				));
			}else{
				throw new NotCallableException(sprintf(
					'Cannot call %s on %s because is not a class nor a valid container entry',
					$callable[1],
					$callable[0]
				));
			}
		}
		
		return $callable;
	}
	
	private function isStaticCallToNonStaticMethod($callable){
		if(is_array($callable) && is_string($callable[0])){
			list($class, $method) = $callable;
			$reflection = new \ReflectionMethod($class, $method);
			
			return ! $reflection->isStatic();
		}
		
		return false;
	}
	
}