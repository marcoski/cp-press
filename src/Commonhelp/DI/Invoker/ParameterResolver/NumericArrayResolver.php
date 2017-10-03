<?php
namespace Commonhelp\DI\Invoker\ParameterResolver;

use ReflectionFunctionAbstract;

class NumericArrayResolver implements ParameterResolverInterface{
	
	public function getParameters(
			ReflectionFunctionAbstract $reflection, 
			array $providedParameters, 
			array $resolvedParameters
	){
		if(!empty($resolvedParameters)){
			$providedParameters = array_diff_key($providedParameters, $resolvedParameters);
		}
		
		foreach($providedParameters as $key => $value){
			if(is_int($key)){
				$resolvedParameters[$key] = $value;
			}
		}
		
		return $resolvedParameters;
	}
	
}