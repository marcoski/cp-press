<?php
namespace Commonhelp\DI\Definition\Source;

use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Definition\ObjectDefinition;
use Commonhelp\DI\Definition\Helper\DefinitionHelperInterface;
use Commonhelp\DI\Definition\FactoryDefinition;
use Commonhelp\DI\Definition\ArrayDefinition;
use Commonhelp\DI\Definition\ValueDefinition;

class DefinitionArray implements DefinitionSourceInterface, MutableDefinitionSourceInterface{
	
	const WILDCARD = '*';
	
	const WILDECARD_PATTERN = '([^\\\\]+)';
	
	private $definitions = array();
	
	public function __construct(array $definitions = array()){
	 $this->definitions = $definitions;
	}
	
	public function addDefinitions(array $definitions){
		$this->definitions = $definitions + $this->definitions;
	}
	
	public function addDefinition(DefinitionInterface $definition){
		$this->definitions[$definition->getName()] = $definition;
	}
	
	public function getDefinition($name){
		if(array_key_exists($name, $this->definitions)){
			return $this->castDefinition($this->definitions[$name], $name);
		}
		
		foreach($this->definitions as $key => $definition){
			if(strpos($key, self::WILDCARD) === false){
				continue;
			}
			
			$key = preg_quote($key);
			$key = '#' . str_replace('\\' . self::WILDCARD, self::WILDECARD_PATTERN, $key) . '#';
			if(preg_match($key, $name, $matches) === 1){
				$definition = $this->castDefinition($definition, $name);
				if($definition instanceof ObjectDefinition){
					array_shift($matches);
					$definition->setClassName(
						$this->replaceWildcards($definition->getClassName(), $matches)
					);
				}
				
				return $definition;
			}
		}
		
		return null;
	}
	
	private function castDefinition($definition, $name){
		if($definition instanceof DefinitionHelperInterface){
			$definition = $definition->getDefinition($name);
		}
		
		if(!$definition instanceof DefinitionInterface && is_array($definition)){
			$definition = new ArrayDefinition($name, $definition);
		}
		
		if($definition instanceof \Closure){
			$definition = new FactoryDefinition($name, $definition);
		}
		
		if(!$definition instanceof DefinitionInterface){
			$definition = new ValueDefinition($name, $definition);
		}
		
		return $definition;
	}
	
	private function replaceWildcards($string, array $replacements){
		foreach($replacements as $replacement){
			$pos = strpos($string, self::WILDCARD);
			if($pos !== false){
				$string = substr_replace($string, $replacement, $pos, 1);
			}
		}
		
		return $string;
	}
	
}