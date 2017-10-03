<?php
namespace Commonhelp\DI\Definition\Invoker;

use Commonhelp\DI\Invoker\ParameterResolver\ParameterResolverInterface;
use Commonhelp\DI\ContainerInterface;
use ReflectionFunctionAbstract;

class FactoryParameterResolver implements ParameterResolverInterface{
	
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	public function __construct(ContainerInterface $container){
		$this->container = $container;
	}
	
	public function getParameters(ReflectionFunctionAbstract $reflection, array $providedParameters, array $resolvedParameters){
		foreach($reflection->getParameters() as $index => $parameter){
			$parameterClass = $parameter->getClass();
			
			if(!$parameterClass){
				continue;
			}
			
			if($parameterClass->name === 'Commonhelp\DI\ContainerInterface'){
				$resolvedParameters[$index] = $this->container;
			}else if($parameterClass->name === 'Commonhelp\DI\Factory\RequestedEntryInterface'){
				$resolvedParameters[$index] = $providedParameters[1];
			}else if($this->container->has($parameter->name)){
				$resolvedParameters[$index] = $this->container->get($parameterClass->name);
			}
		}
		
		return $resolvedParameters;
	}
	
}