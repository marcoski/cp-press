<?php

namespace Commonhelp\Config;

use Commonhelp\Config\Configurator\Parser\ParserInterface;

abstract class WriteableConfig extends Config{
	
	protected $writeable = true;
	protected $file = null;
	protected $parser = null;
	
	public function write(){
		$this->parser->write($this->file, $this->container);
	}
	
	public function setWriteable(){
		$this->writeable = true;
	}
	
	public function setNotWriteable(){
		$this->writeable = false;
	}
	
	public function isWriteable(){
		return $this->writeable;
	}
	
	public function setFile($file){
		$this->file = $file;
	}
	
	public function setParser(ParserInterface $parser){
		$this->parser = $parser;
	}
	
	public function isValidMethod($method){
		return in_array($method, $this->validMethodList);
	}
}

