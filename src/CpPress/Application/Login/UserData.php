<?php
namespace CpPress\Application\Login;

class UserData implements UserDataInterface, \ArrayAccess, \Countable{
	
	private $data;
	
	public function __construct(array $data = array()){
		$this->data = $data;
	}
	
	public function get($name){
		if($this->has($name)){
			return $this->data[$name];
		}
	}
	
	public function has($name){
		return isset($this->data[$name]);
	}
	
	public function set($name, $value){
		$this->data[$name] = $value;
	}
	
	public function all(){
		return $this->data;
	}
	
	public function remove($name){
		if($this->has($name)){
			unset($this->data[$name]);
		}
	}
	
	public function offsetGet($name){
		return $this->get($name);
	}
	
	public function offsetSet($name, $value){
		$this->set($name, $value);
	}
	
	public function offsetExists($name){
		return $this->has($name);
	}
	
	public function offsetUnset($name){
		$this->remove($name);
	}
	
	public function count(){
		return count($this->data);
	}
	
}