<?php
namespace Commonhelp\DI\Invoker\ParameterResolver;

use ReflectionFunctionAbstract;

/**
 * Chain of responsability pattern
 */
class ResolveChain implements ParameterResolverInterface{
	
	/**
	 * @var ParameterResolverInterface[]
	 */
	private $resolvers = array();
	
	public function __construct(array $resolvers = array()){
		$this->resolvers = $resolvers;
	}
	
	public function getParameters(
			ReflectionFunctionAbstract $reflection, 
			array $providedParameters, 
			array $resolvedParameters
	){
		$reflectionParameters = $reflection->getParameters();
		
		foreach($this->resolvers as $resolver){
			$resolvedParameters = $resolver->getParameters($reflection, $providedParameters, $resolvedParameters);
			
			$diff = array_diff_key($reflectionParameters, $resolvedParameters);
			if(empty($this)){
				return $resolvedParameters;
			}
		}
		
		return $resolvedParameters;
	}
	
	public function appendResolver(ParameterResolverInterface $resolver){
		$this->resolvers[] = $resolver;
	}
	
	public function prependResolver(ParameterResolverInterface $resolver){
		array_unshift($this->resolvers, $resolver);
	}
	
}