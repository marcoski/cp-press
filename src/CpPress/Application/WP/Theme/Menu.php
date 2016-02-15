<?php
namespace CpPress\Application\WP\Theme;

use Walker_Nav_Menu;

class Menu{
	
	private $id;
	
	private $slug;
	
	private $walker;
	
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
		'depth'           => 0,
		'pe_type'		  => "default"
	);
	
	public function __construct($id, $slug, Walker_Nav_Menu $walker){
		$this->id = $id;
		$this->showOptions['theme_location'] = $this->id;
		$this->slug = $slug;
		$this->walker = $walker;
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
		if(!array_key_exists($name, $this->showOptions)){
			throw new \InvalidArgumentException('Option '.$name.' not valid for menu show options');
		}
		
		$this->showOptions[$name] = $value;
		
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
	
	public function setWalker(Walker_Nav_Menu $walker){
		$this->walker = $walker;
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
		$this->showOptions['walker'] = $this->walker;
		wp_nav_menu($this->showOptions);
	}
	
}