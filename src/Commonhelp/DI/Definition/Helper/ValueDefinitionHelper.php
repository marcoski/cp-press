<?php
namespace Commonhelp\DI\Definition\Helper;

use Commonhelp\DI\Definition\ValueDefinition;

class ValueDefinitionHelper implements DefinitionHelperInterface{
	
	private $value;
	
	public function __construct($value){
		$this->value = $value;
	}
	
	public function getDefinition($entryName){
		return new ValueDefinition($entryName, $this->value);
	}
	
}