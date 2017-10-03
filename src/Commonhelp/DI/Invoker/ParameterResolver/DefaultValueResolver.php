<?php
namespace Commonhelp\DI\Invoker\ParameterResolver;

use ReflectionFunctionAbstract;
use ReflectionException;

class DefaultValueResolver implements ParameterResolverInterface{
	
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
			if($parameter->isOptional()){
				try{
					$resolvedParameters[$index] = $parameter->getDefaultValue();
				}catch(ReflectionException $e){
				}
			}
		}
		
		return $resolvedParameters;
	}
	
}