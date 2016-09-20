<?php
namespace Commonhelp\DI\Definition;

use Commonhelp\DI\Scope;
class AliasDefinition implements CachableDefinitionInterface{
	
	private $name;
	
	private $targetEntryName;
	
	public function __construct($name, $targetEntryName){
		$this->name = $name;
		$this->targetEntryName = $targetEntryName;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getScope(){
		return Scope::PROTOTYPE;
	}
	
	public function getTargetEntryName(){
		return $this->targetEntryName;
	}
	
}