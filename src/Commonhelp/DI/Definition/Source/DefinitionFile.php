<?php
namespace Commonhelp\DI\Definition\Source;

use Commonhelp\DI\Definition\Exception\DefinitionException;

class DefinitionFile extends DefinitionArray{

	private $initialized = false;
	
	private $file;
	
	public function __construct($file){
		$this->file = $file;
		parent::__construct(array());
	}
	
	public function getDefinition($name){
		$this->initialize();
		
		return parent::getDefinition($name);
	}
	
	private function initialize(){
		if($this->initialized === true){
			return;
		}
		$definitions = require $this->file;
		if(!is_array($definitions)){
			throw new DefinitionException("File {$this->file} should return an array of definitions");
		}
		
		$this->addDefinitions($definitions);
		$this->initialized = true;
	}

}