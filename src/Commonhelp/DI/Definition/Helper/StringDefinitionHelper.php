<?php
namespace Commonhelp\DI\Definition\Helper;

use Commonhelp\DI\Definition\StringDefinition;

class StringDefinitionHelper implements DefinitionHelperInterface{
	
	private $expression;
	
	public function __construct($expression){
		$this->expression = $expression;
	}
	
	public function getDefinition($entryName){
		return new StringDefinition($entryName, $this->expression);
	}
	
}