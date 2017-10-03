<?php
namespace Commonhelp\DI\Annotations;

use Commonhelp\Util\Annotations\AnnotationsInterface;

class AnnotationsBuilder{
	
	/**
	 * 
	 * @var AnnotationsInterface[] $annotations
	 */
	private $annotations = array();
	
	public function __construct($object){
		$this->annotations['Type'] = new TypeAnnotations($object);
	}
	
	public function parse(){
		foreach($this->annotations as $annotation){
			$annotation->parse();
		}
	}
	
	public function get($annotation, \Closure $closure){
		if($this->has($annotation)){
			$this->annotations[$annotation]->parse();
			return $closure($this->annotations[$annotation]);
		}
		
		return null;
	}
	
	public function has($annotation){
		return isset($this->annotations[$annotation]);
	}
	
	public function set($annotation, $object){
		if(!$this->has($annotation) && class_exists($annotation)){
			$this->annotations[$annotation] = new $annotation($object);
		}
	}
	
	
}