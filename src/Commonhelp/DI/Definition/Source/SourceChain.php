<?php
namespace Commonhelp\DI\Definition\Source;

use Commonhelp\DI\Definition\DefinitionInterface;
use Commonhelp\DI\Definition\SubDefinitionInterface;

class SourceChain implements DefinitionSourceInterface, MutableDefinitionSourceInterface{
	
	/**
	 * @var DefinitionSourceInterface[]
	 */
	private $sources;
	
	/**
	 * @var DefinitionSourceInterface
	 */
	private $rootSource;
	
	/**
	 * @var MutableDefinitionSourceInterface
	 */
	private $mutableSource;
	
	public function __construct(array $sources){
		$this->sources = array_values($sources);
		$this->rootSource = $this;
	}
	
	public function getDefinition($name, $startIndex = 0){
		$count = count($this->sources);
		for($i = $startIndex; $i < $count; $i++){
			$source = $this->sources[$i];
			$definition = $source->getDefinition($name);
			if($definition){
				if($definition instanceof SubDefinitionInterface){
					$this->resolveSubDefinition($definition, $i);
				}
				
				return $definition;
			}
		}
		
		return null;
	}
	
	public function addDefinition(DefinitionInterface $definition){
		if(!$this->mutableSource){
			throw new \LogicException('The container\'s definition source has not been initialized correctly');
		}
		
		$this->mutableSource->addDefinition($definition);
	}
	
	public function setRootDefinitionSource(DefinitionSourceInterface $rootSource){
		$this->rootSource = $rootSource;
	}
	
	private function resolveRootDefinition(DefinitionSourceInterface $rootSource){
		$this->rootSource = $rootSource;
	}
	
	private function resolveSubDefinition(SubDefinitionInterface $definition, $currentIndex){
		$subDefinitionName = $definition->getSubDefinitionName();
		
		if($subDefinitionName === $definition->getName()){
			$subDefinition = $this->getDefinition($subDefinitionName, $currentIndex + 1);
		}else{
			$subDefinition = $this->rootSource->getDefinition($subDefinitionName);
		}
		
		if($subDefinition){
			$definition->setSubDefinition($definition);
		}
	}
	
	public function setMutableDefinitionSource(MutableDefinitionSourceInterface $mutableSource){
		$this->mutableSource = $mutableSource;
		array_unshift($this->sources, $mutableSource);
	}
	
}