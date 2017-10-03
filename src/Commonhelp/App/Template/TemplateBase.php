<?php

namespace Commonhelp\App\Template;

use Commonhelp\App\AbstractController;
use Commonhelp\App\Exception\TemplateNullException;
abstract class TemplateBase implements TemplateInterface{
	
	protected $template;
	protected $vars;
	
	protected $globals;
	
	protected $path;
	
	public function __construct($template=null){
		$this->vars = array();
		$this->template = $template;
	}
	
	public function setTemplate($template){
		$this->template = $template;
	}
	
	public function getTemplate(){
		return $this->template;
	}
	
	public function setVars(array $vars){
		$this->vars = $vars;
	}
	
	public function getVars(){
		return $this->vars;
	}
	
	public function render(){
		if(is_null($this->template)){
			throw new TemplateNullException('Empty');
		}
		return $this->load($this->template);
	}
	
	public function assign($key, $value) {
		$this->vars[$key] = $value;
		return true;
	}
	
	public function addGlobal($key, $value){
		$this->globals[$key] = $value;
	}
	
	public function getGlobals(){
		return $this->globals;
	}
	
	protected function load($file, $vars=array()){
		//Register variable
		if(empty($vars)){
			extract($this->vars);
		}else{
			extract($vars);
		}
		
		ob_start();
		include $file;
		$data = ob_get_contents();
		@ob_end_clean();
		// Return data
		
		return $data;
	}
	
	protected static function getHttpProtocol() {
		$claimedProtocol = strtoupper($_SERVER['SERVER_PROTOCOL']);
		$validProtocols = [
				'HTTP/1.0',
				'HTTP/1.1',
				'HTTP/2',
		];
		if(in_array($claimedProtocol, $validProtocols, true)) {
			return $claimedProtocol;
		}
		return 'HTTP/1.1';
	}
	
	abstract protected function findTemplate($name, $app='');
	
	abstract public function inc($template, $additionalParams);
	
}