<?php
namespace CpPress\Application\WP\Theme;

class Sidebar{
	
	private $name;
	
	private $id;
	
	private $description = '';
	
	private $class = '';
	
	private $beforeWidget = '<li id="%1$s" class="widget %2$s">';
	
	private $afterWidget = '</li>';
	
	private $beforeTitle = '<h2 class="widgettitle">';
	
	private $afterTitle = '</h2>';
	
	public function __construct($id, $name){
		$this->name = $name;
		$this->id = $id;
	}
	
	public function register(){
		return register_sidebar(get_class_vars(get_class($this)));
	}
	
	public function unregister(){
		unregister_sidebar($this->id);
	}
	
	public function show(){
		return dynamic_sidebar($this->id);
	}
	
	public function isActive(){
		return is_active_sidebar($this->id);
	}
	
	public function __get($name){
		if(property_exists($this, $name)){
			return $this->$name;
		}
	}
	
	public function __set($name, $value){
		if(property_exists($this, $name)){
			$this->$name = $value;
		}
	}
}