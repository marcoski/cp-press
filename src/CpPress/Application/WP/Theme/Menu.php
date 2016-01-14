<?php
namespace CpPress\Application\WP\Theme;

use Walker;

class Menu{
	
	private $id;
	
	private $slug;
	
	private $showOptions = array(
		'theme_location'  => "",
		'menu'            => "",
		'container'       => "",
		'container_class' => "",
		'container_id'    => "",
		'menu_class'      => null,
		'menu_id'         => null,
		'echo'            => true,
		'before'          => "",
		'after'           => "",
		'link_before'     => "",
		'link_after'      => "",
		'items_wrap'      => "",
		'walker'		  => null,
		'depth'           => 0,
		'pe_type'		  => "default"
	);
	
	public function __construct($id, $slug){
		$this->id = $id;
		$this->showOptions['theme_location'] = $this->id;
		$this->slug = $slug;
	}
	
	public function getId(){
		return $id;
	}
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function getSlug(){
		return $this->slug;
	}
	
	public function setSlug($slug){
		$this->slug = $slug;
	}
	
	public function setShowOption($name, $value){
		if(array_key_exists($name, $this->showOptions)){
			$this->showOptions[$name] = $value;
		}
		
		throw new \InvalidArgumentException('Option '.$name.' not valid for menu show options');
	}
	
	public function getShowOption($name){
		if(array_key_exists($name, $this->showOptions)){
			return $this->showOptions[$name];
		}
		
		throw new \InvalidArgumentException('Option '.$name.' not valid for menu show options');
	}
	
	public function setShowOptions(array $options){
		$this->showOptions = $options;
	}
	
	public function getShowOptions(){
		return $this->showOptions;
	}
	
	public function setWalker(Walker $walker){
		$this->showOptions['walker'] = $walker;
	}
	
	public function register(){
		register_nav_menu($this->id, $this->slug);
	}
	
	public function unregister(){
		unregister_nav_menu($this->id);
	}
	
	public function exists(){
		return has_nav_menu($this->id);
	}
	
	public function show(){
		wp_nav_menu($this->showOptions);
	}
	
}