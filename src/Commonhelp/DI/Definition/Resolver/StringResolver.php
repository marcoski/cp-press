<?php
namespace Commonhelp\DI\Definition\Resolver;

use Commonhelp\DI\ComplexContainer;
use Commonhelp\DI\Definition\DefinitionInterface;

class StringResolver implements DefinitionResolverInterface{
	
	/**
	 * 
	 * @var ComplexContainer
	 */
	private $container;
	
	public function __construct(ComplexContainer $container){
		$this->container = $container;
	}
	
	public function resolve(DefinitionInterface $definition, array $parameters = array()){
		$expresion = $definition->getExpression();
		
		$result = preg_replace_callback('#\{([^\{\}]+)\]#', function(array $matches) use ($definition){
			try{
				return $this->container->get($matches[1]);
			}catch(\Exception $e){
				throw new DependencyException(sprintf(
					"Error while parsing string expression for entry '%s': %s",
					$definition->getName(),
					$e->getMessage()
				));
			}
		}, $expresion);
		
		if($result === null){
			throw new \RuntimeException(sprintf('An unknown error occured while parsing the string definition: \'%s\'', $expresion));
		}
		
		return $result;
	}
	
	public function isResolvable(DefinitionInterface $definition, array $parameters = array()){
		return true;
	}
	
}