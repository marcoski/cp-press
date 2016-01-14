<?php
namespace CpPress\Application\WP\Admin;

class Options{
	
	private $option;
	
	private static $instances = array();
	
	public function __construct($option){
		$this->option = $option;
	}
	
	public static function instance($option){
		if(isset(self::$instances[$option])){
			return self::$instances[$option];
		}
		
		self::$instances[$option] = new static($option);
		return self::$instances[$option];
	}
	
	public static function addOption($option, $value, $autoload='yes'){
		if(isset(self::$instances[$option])){
			return self::$instances->add($value, $autoload);
		}
	
		self::$instances[$option] = new static($option);
		return self::$instances[$option]->add($value, $autoload);
	}
	
	public function add($value, $autoload='yes'){
		return add_option($this->option, $value, '', $autoload);
	}
	
	public static function deleteOption($option){
		if(isset(self::$instances[$option])){
			return self::$instances->delete();
		}
	
		self::$instances[$option] = new static($option);
		return self::$instances[$option]->delete();
	}
	
	public function delete(){
		return delete_option($this->option);
	}
	
	public static function updateOption($option, $newValue, $autoload='yes'){
		if(isset(self::$instance[$option])){
			return self::$instances->update($newValue, $autoload);
		}
	
		self::$instances[$option] = new static($option);
		return self::$instances[$option]->update($newValue, $autoload);
	}
	
	public function update($newValue, $autoload='yes'){
		return update_option($this->option, $newValue, $autoload);
	}
	
	public static function getOption($option, $default=false){
		if(isset(self::$instances[$option])){
			return self::$instances->get($default);
		}
	
		self::$instances[$option] = new static($option);
		return self::$instances[$option]->get($default);
	}
	
	public function get($default=false){
		return get_option($this->option, $default);
	}
	
	public static function toggleAutoLoadOption(){
		if(isset(self::$instances[$option])){
			return self::$instances->toggleAutoLoad();
		}
		
		self::$instances[$option] = new static($option);
		return self::$instances[$option]->toggleAutoLoad();
	}
	
	public function toggleAutoLoad(){
		$old = $this->get();
		if($this->delete()){
			return $this->update($old, 'no');
		}
		
		return false;
	}
	
}