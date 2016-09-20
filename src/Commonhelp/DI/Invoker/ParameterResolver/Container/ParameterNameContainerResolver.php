<?php
namespace Commonhelp\DI\Invoker\ParameterResolver\Container;

use Commonhelp\DI\Invoker\ParameterResolver\ParameterResolverInterface;
use Commonhelp\DI\ContainerInterface;
use ReflectionFunctionAbstract;

class ParameterNameContainerResolver implements ParameterResolverInterface{
	
	/**
	 * @var ContainerInterface;
	 */
	private $container;
	
	public function __construct(ContainerInterface $container){
		$this->container = $container;
	}
	
	public function getParameters(ReflectionFunctionAbstract $reflection, $providedParameters, $resolvedParameters){
		$parameters = $reflection->getParameters();
		
		if(!empty($resolvedParameters)){
			$parameters = array_diff_key($parameters, $resolvedParameters);
		}
		
		foreach($parameters as $index => $parameter){
			$name = $parameter->name;
			if($name && $this->container->has($name)){
				$resolvedParameters[$index] = $this->container->get($name);
			}
		}
		
		return $resolvedParameters;
	}
	
}