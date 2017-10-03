<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Definition\Helper\DefinitionHelperInterface;

class EnviromentVariabelResolver implements DefinitionResolverInterface{
	
	/**
	 * 
	 * @var DefinitionResolverInterface $deifnitionResolver
	 */
	private $definitionResolver;
	
	private $variableReader;
	
	public function __construct(DefinitionResolverInterface $definitionResolver, $variableReader = 'getenv'){
		$this->definitionResolver = $definitionResolver;
		$this->variableReader = $variableReader;
	}
	
	public function resolve(DefinitionInterface $definition, array $parameters = array()){
		$value = call_user_func($this->variableReader, $definition->getVariableName());
		
		if(false !== $value){
			return $value;
		}
		
		if(!$definition->isOptional()){
			throw new DefinitionException(sprintf(
				'The Enviroment Variable "%s" hae not been defined',
				$definition->getVariableName()
			));
		}
		
		$value = $definition->getVariableName();
		
		if($value instanceof DefinitionHelperInterface){
			return $this->definitionResolver->resolve($value->getDefinition(''));
		}
		
		return $value;
	}
	
	public function isResolvable(DefinitionInterface $definition, array $parameters = array()){
		return true;
	}
}