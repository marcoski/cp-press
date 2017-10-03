<?php
namespace Commonhelp\DI\Invoker;

use Commonhelp\DI\Invoker\ParameterResolver\ParameterResolverInterface;
use Commonhelp\DI\ContainerInterface;
use Commonhelp\DI\Invoker\ParameterResolver\ResolveChain;
use Commonhelp\DI\Invoker\ParameterResolver\NumericArrayResolver;
use Commonhelp\DI\Invoker\ParameterResolver\AssociativeArrayResolver;
use Commonhelp\DI\Invoker\ParameterResolver\DefaultValueResolver;
use Commonhelp\DI\Invoker\Exception\NotCallableException;
use Commonhelp\DI\Invoker\Reflection\CallableReflection;
use Commonhelp\DI\Invoker\Exception\NotEnoughParametersException;

class Invoker implements InvokerInterface{
	
	/**
	 * @var CallableResolver | null
	 */
	private $callableResolver;
	
	/**
	 * @var ParameterResolverInterface | null
	 */
	private $parameterResolver;
	
	/**
	 * @var ContainerInterface | null
	 */
	private $container;
	
	public function __construct(ParameterResolverInterface $parameterResolver = null, ContainerInterface $container = null){
		$this->parameterResolver = $parameterResolver ?: $this->createParameterResolver();
		$this->container = $container;
		
		if($container){
			$this->callableResolver = new CallableResolver($container);
		}
	}
	
	public function call($callable, array $parameters = array()){
		if($this->callableResolver){
			$callable = $this->callableResolver->resolve($callable);
		}
		
		if(!is_callable($callable)){
			throw new NotCallableException(sprintf(
				'"%s" is not callable',
				is_object($callable) ? 'Instance of ' . get_class($callable) : var_export($callable, true)
			));
		}
		
		$callableReflection = CallableReflection::create($callable);
		
		$args = $this->parameterResolver->getParameters($callableReflection, $parameters, array());
		ksort($args);
		
		$diff = array_diff_key($callableReflection->getParameters(), $args);
		if(!empty($diff)){
			/** @var \ReflectionParameter $parameter */
			$parameter = reset($diff);
			throw new NotEnoughParametersException(sprintf(
				'Unable to invoke the callable beacuse no value was given for paramater %d (%s)',
				$parameter->getPosition() + 1,
				$parameter->name
			));
		}
		
		return call_user_func_array($callable, $args);
	}
	
	/**
	 * @return ParameterResolverInterface
	 */
	public function getParameterResolver(){
		return $this->parameterResolver;
	}
	
	/**
	 * @return ContainerInterface
	 */
	public function getContainer(){
		return $this->container;
	}
	
	/**
	 * @return CallableResolver
	 */
	public function getCallableResolver(){
		return $this->callableResolver;
	}
	
	/**
	 * Creates the default parameter resolver
	 * @return ParameterResolverInterface
	 */
	private function createParameterResolver(){
		return new ResolveChain(array(
			new NumericArrayResolver(),
			new AssociativeArrayResolver(),
			new DefaultValueResolver()
		));
	}
	
}