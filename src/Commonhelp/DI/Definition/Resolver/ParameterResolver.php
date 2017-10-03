<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\Definition\Exception\DefinitionException;
use Commonhelp\DI\Definition\Exception\Commonhelp\DI\Definition\Exception;
use Commonhelp\DI\Definition\Helper\DefinitionHelperInterface;
use Commonhelp\DI\Definition\ObjectDefinition\MethodInjection;

class ParameterResolver{
	
	/**
	 * 
	 * @var DefinitionResolverInterface
	 */
	private $definitionResolver;
	
	public function __construct(DefinitionResolverInterface $definitionResolver){
		$this->definitionResolver = $definitionResolver;
	}
	
	
	/**
	 * 
	 * @param MethodInjection $definition
	 * @param \ReflectionMethod $method
	 * @param array $parameters
	 * @throws DefinitionException
	 * @return array Parameters to user to call the function
	 */
	public function resolveParameters(MethodInjection $definition = null, \ReflectionMethod $method = null, array $parameters = array()){
		$args = [];
		if(!$method){
			return $args;
		}
		
		$definitionParameters = $definition ? $definition->getParameters() : array();
		foreach($method->getParameters() as $index => $parameter){
			if(array_key_exists($parameter->getName(), $parameters)){
				$value = &$parameters[$parameter->getName()];
			}else if(array_key_exists($index, $definitionParameters)){
				$value = &$definitionParameters[$index];
			}else{
				if($parameter->isOptional()){
					$args[] = $this->getParameterDefaultValue($parameter, $method);
					continue;
				}
				throw new DefinitionException(sprintf(
					'Parameter %s of %s::%s has no value defined or guessable',
					$parameter->getName(),
					$method->getDeclaringClass()->getName(),
					$this->getFunctionName($method)
				));
			}
			
			if($value instanceof DefinitionHelperInterface){
				$nestedDefinition = $value->getDefinition('');
				
				if($parameter->isOptional() && !$this->definitionResolver->isResolvable($nestedDefinition)){
					$value = $this->getParameterDefaultValue($parameter, $method);
				}else{
					$value = $this->definitionResolver->resolve($nestedDefinition);
				}
			}
			
			$args[] = &$value;
		}
		
		return $args;
	}
	
	private function getParameterDefaultValue(\ReflectionParameter $parameter, \ReflectionMethod $function){
		try{
			return $parameter->getDefaultValue();
		}catch(\ReflectionException $e){
			throw new DefinitionException(sprintf(
				'The parameter %s of %s has no type defined or guessable. It has a default value, '
				. 'but the default value can\'t be read through Reflection because it is a PHP internal class.',
				$parameter->getName(),
				$this->getFunctionName($function)
			));
		}
	}
	
	private function getFunctionName(\ReflectionMethod $method){
		return $method->getName() . '()';
	}
}