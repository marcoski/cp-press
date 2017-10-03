<?php
namespace Commonhelp\DI\Invoker\ParameterResolver\Container;

use Commonhelp\DI\Invoker\ParameterResolver\ParameterResolverInterface;
use Commonhelp\DI\ContainerInterface;
use ReflectionFunctionAbstract;

class TypeHintContainerResolver implements ParameterResolverInterface{
	
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	public function __construct(ContainerInterface $container){
		$this->container = $container;
	}
	
	public function getParameters(ReflectionFunctionAbstract $reflection, $providedParameters, $resolvedParameters){
		$parameters = $reflection->getParameters();
		
		if(!empty($resolvedParameters)){
			$parameters = array_diff_key($parameters, $resolvedParameters);
			
			foreach($parameters as $index => $parameter){
				$parameterClass = $parameter->getClass();
				
				if($parameterClass && $this->container->has($parameterClass->name)){
					$resolvedParameters[$index] = $this->container->get($parameterClass->name);
				}
			}
			
			return $resolvedParameters;
		}
	}
	
}