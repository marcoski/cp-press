<?php
namespace Commonhelp\DI\Invoker\Reflection;

class CallableReflection{
	
	public static function create($callable){
		
		if($callable instanceof \Closure){
			return new \ReflectionFunction($callable);
		}
		
		if(is_array($callable)){
			list($class, $method) = $callable;
			return new \ReflectionMethod($class, $method);
		}
		
		if(is_object($callable) && method_exists($callable, '__invoke')){
			return new \ReflectionMethod($callable, '__invoke');
		}
		
		if(is_string($callable) && function_exists($callable)){
			return new \ReflectionFunction($callable);
		}
		
		throw new NotCallableException(sprintf(
			'%s is not callable',
			is_string($callable) ? $callable : 'Instance of ' . get_class($callable)
		));
	}
	
}