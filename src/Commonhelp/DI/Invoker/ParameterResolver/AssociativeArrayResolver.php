<?php
namespace Commonhelp\DI\Invoker\ParameterResolver;

use ReflectionFunctionAbstract;

class AssociativeArrayResolver implements ParameterResolverInterface{
	
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
			if(array_key_exists($parameter->name, $providedParameters)){
				$resolvedParameters[$index] = $providedParameters[$parameter->name];
			}
		}
		
		return $resolvedParameters;
	}
	
}