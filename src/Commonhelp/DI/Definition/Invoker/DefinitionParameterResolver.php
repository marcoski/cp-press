<?php
namespace Commonhelp\DI\Definition\Invoker;

use Commonhelp\DI\Invoker\ParameterResolver\ParameterResolverInterface;
use Commonhelp\DI\Definition\Resolver\DefinitionResolverInterface;
use ReflectionFunctionAbstract;
use Commonhelp\DI\Definition\Helper\DefinitionHelperInterface;

class DefinitionParameterResolver implements ParameterResolverInterface{
	
	/**
	 * @var DefinitionResolverInterface
	 */
	private $definitionResolver;
	
	public function __construct(DefinitionResolverInterface $definitionResolver){
		$this->definitionResolver = $definitionResolver;
	}
	
	public function getParameters(ReflectionFunctionAbstract $reflection, array $providedParameters, array $resolvedParameters){
		if(!empty($resolvedParameters)){
			$providedParameters = array_diff_key($providedParameters, $resolvedParameters);
		}
		
		foreach($providedParameters as $key => $value){
			if(!$value instanceof DefinitionHelperInterface){
				continue;
			}
			
			$definition = $value->getDefinition('');
			$value = $this->definitionResolver->resolve($definition);
			
			if(is_int($key)){
				$resolvedParameters[$key] = $value;
			}else{
				$reflectionParameters = $reflection->getParameters();
				foreach($reflectionParameters as $reflectionParameter){
					if($key === $reflectionParameter->name){
						$resolvedParameters[$reflectionParameter->getPosition()] = $value;
					}
				}
			}
		}
		
		return $resolvedParameters;
	}
	
}