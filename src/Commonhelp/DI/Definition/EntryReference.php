<?php
namespace Commonhelp\DI\Definition;

use Commonhelp\DI\Definition\Helper\DefinitionHelperInterface;

class EntryReference implements DefinitionHelperInterface{
	
	private $name;

	public function __construct($entryName){
		$this->name = $entryName;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getDefinition($entryName){
		return new AliasDefinition($entryName, $this->name);
	}
}