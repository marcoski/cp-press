<?php
namespace Commonhelp\DI\Invoker\ParameterResolver;

use ReflectionFunctionAbstract;

class TypeHintResolver implements ParameterResolverInterface{
	
	public function getParameters(
			ReflectionFunctionAbstract $reflection, 
			array $providedParameters, 
			array $resolvedParameters
	){
		$parameters = $reflection->getParameters();
		
		if(!empty($resolvedParameters)){
			$parameters = array_diff_key($parameters, $resolvedParameters);
		}
		
		foreach($parameters as $index => $parameter){
			$parameterClass = $parameter->getClass();
			if($parameterClass && array_key_exists($parameterClass->name, $providedParameters)){
				$resolvedParameters[$index] = $providedParameters[$parameterClass->name];
			}
		}
		
		return $resolvedParameters;
	}
}