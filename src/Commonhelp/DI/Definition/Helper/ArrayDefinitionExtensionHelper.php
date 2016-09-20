<?php
namespace Commonhelp\DI\Definition\Helper;

use Commonhelp\DI\Definition\ArrayDefinitionExtension;

class ArrayDefinitionExtensionHelper implements DefinitionHelperInterface{
	
	private $values = array();
	
	public function __construct(array $values){
		$this->values = $values;
	}
	
	
	public function getDefinition($entryName){
		return new ArrayDefinitionExtension($entryName, $values);
	}
	
}