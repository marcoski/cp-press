<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Definition\ArrayDefinition;
use Commonhelp\DI\Definition\Helper\DefinitionHelperInterface;

class ArrayResolver implements DefinitionResolverInterface{
	
	/**
	 * 
	 * @var DefinitionResolverInterface
	 */
	private $definitionResolver;
	
	public function __construct(DefinitionResolverInterface $definitionResolver){
		$this->definitionResolver = $definitionResolver;
	}
	
	/**
	 * @param ArrayDefinition
	 */
	public function resolve(DefinitionInterface $definition, array $parameters = array()){
		$values = $definition->getValues();
		$values = $this->resolveNestedDefinition($definition, $values);
		
		return $values;
	}
	
	public function isResolvable(DefinitionInterface $definition, array $parameters = array()){
		return true;
	}
	
	private function resolveNestedDefinition(ArrayDefinition $definition, array $values){
		foreach($values as $key => $value){
			if($value instanceof DefinitionHelperInterface){
				$values[$key] = $this->resolveDefinition($value, $definition, $key);
			}
		}
		
		return $values;
	}
	
	private function resolveDefinition(DefinitionHelperInterface $value, ArrayDefinition $definition, $key){
		try{
			return $this->definitionResolver->resolve($value->getDefinition(''));
		}catch(DependencyException $e){
			throw $e;
		}catch(\Exception $e){
			throw new DependencyException(sprintf(
				'Error while resolving %s[%s].',
				$definition->getName(),
				$key
			));
		}
	}
	
}