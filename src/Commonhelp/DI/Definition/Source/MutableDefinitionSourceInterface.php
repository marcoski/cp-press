<?php
namespace Commonhelp\DI\Definition\Source;

use Commonhelp\DI\Definition\DefinitionInterface;

interface MutableDefinitionSourceInterface extends DefinitionSourceInterface{
	
	public function addDefinition(DefinitionInterface $definition);
	
}