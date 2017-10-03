<?php
namespace Commonhelp\DI\Annotations;

use Commonhelp\Util\Annotations\AbstractAnnotations;

class TypeAnnotations extends AbstractAnnotations{
	
	const TYPE_REGEXP = '/@param\h+(?P<type>\w+)\h+\$(?P<var>\w+)/';
	
	private $types;
	
	public function __construct($object){
		$this->init($object);
		$this->types = array();
	}
	
	public function allTypes(){
		return $this->types;
	}
	
	public function allTypesByMethod($method){
		if(isset($this->types[$method])){
			return $this->types[$method];
		}
		
		return null;
	}
	
	public function hasType($type, $method){
		if(isset($this->types[$method])){
			foreach($this->types[$method] as $name => $t){
				if($type === $name){
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function getType($type, $method){
		if(isset($this->types[$method])){
			foreach($this->types[$method] as $name => $t){
				if($type === $name){
					return $t;
				}
			}
		}
		
		return null;
	}
	
	public function parse(){
		foreach($this->methods as $name => $a){
			preg_match_all(self::TYPE_REGEXP, $a, $matches);
			// this is just a fix for PHP 5.3 (array_combine raises warning if called with
			// two empty arrays
			if(!($matches['var'] === array() && $matches['type'] === array())) {
				$this->types[$name] = array_combine($matches['var'], $matches['type']);
			}
		}
	}
}