<?php
namespace Commonhelp\Util\Annotations;

class ControllerMethodAnnotations extends AbstractAnnotations{
	
	private $types;
	private $parameters;
	private $responder;
	
	private $method;
	
	public function __construct(){
		$this->types = array();
		$this->parameters = array();
	}
	
	public function initMethod($object, $method){
		parent::init($object);
		$this->method = $method;
	}
	
	/**
	 * @param object $object an object or classname
	 * @param string $method the method which we want to inspect
	 */
	public function parse(){
		// extract everything prefixed by @ and first letter uppercase
		preg_match_all('/@([A-Z]\w+)/', $this->get($this->method), $matches);
		$this->annotations = $matches[1];
		// extract type parameter information
		preg_match_all('/@param\h+(?P<type>\w+)\h+\$(?P<var>\w+)/', $this->get($this->method), $matches);
		// this is just a fix for PHP 5.3 (array_combine raises warning if called with
		// two empty arrays
		if($matches['var'] === array() && $matches['type'] === array()) {
			$this->types = array();
		} else {
			$this->types = array_combine($matches['var'], $matches['type']);
		}
		
		// get method parameters
		$reflection = $this->reflector->getMethod($this->method);
		foreach ($reflection->getParameters() as $param) {
			if($param->isOptional()) {
				$default = $param->getDefaultValue();
			} else {
				$default = null;
			}
			$this->parameters[$param->name] = $default;
		}
		// extract return parameter information
		preg_match_all("/@responder\h+(?P<responder>\w+)/", $this->get($this->method), $matches);
		if($matches['responder'] === array()){
			$this->responder = null;
		}else{
			$this->responder = array_pop($matches['responder']);	
		}
	}
	
	/**
	 * Inspects the PHPDoc parameters for types
	 * @param string $parameter the parameter whose type comments should be
	 * parsed
	 * @return string|null type in the type parameters (@param int $something)
	 * would return int or null if not existing
	 */
	public function getType($parameter) {
		if(array_key_exists($parameter, $this->types)) {
			return $this->types[$parameter];
		} else {
			return null;
		}
	}
	
	/**
	 * @return array the arguments of the method with key => default value
	 */
	public function getParameters() {
		return $this->parameters;
	}
	
	public function getResponder() {
		return $this->responder;
	}
	
	/**
	 * Check if a method contains an annotation
	 * @param string $name the name of the annotation
	 * @return bool true if the annotation is found
	 */
	public function hasAnnotation($name){
		return in_array($name, $this->annotations);
	}
	
	
}