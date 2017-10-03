<?php
namespace Commonhelp\App\Http\Session\Flash;

class FlashBag implements FlashBagInterface{
	
	private $name = 'flashes';
	
	private $flashes = array();
	
	private $storageKey;
	
	public function __construct($storageKey = '_ch_flashes'){
		$this->storageKey = $storageKey;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function initialize(array &$flashes){
		$this->flashes = &$flashes;
	}
	
	public function add($type, $message){
		$this->flashes[$type][] = $message;
	}
	
	public function peek($type, array $default = array()){
		return $this->has($type) ? $this->flashes[$type] : $default;
	}
	
	public function peekAll(){
		return $this->flashes;
	}
	
	public function get($type, array $default = array()){
		if(!$this->has($type)){
			return $default;
		}
		
		$return = $this->flashes[$type];
		unset($this->flashes[$type]);
		
		return $return;
	}
	
	public function all(){
		$return = $this->peekAll();
		$this->flashes = array();
		
		return $return;
	}
	
	public function set($type, $message){
		$this->flashes[$type] = (array) $message;
	}
	
	public function setAll(array $message){
		$this->flashes = $message;
	}
	
	public function has($type){
		return array_key_exists($type, $this->flashes) && $this->flashes[$type];
	}
	
	public function keys(){
		return array_keys($this->flashes);
	}
	
	public function getStorageKey(){
		return $this->storageKey;
	}
	
	public function clear(){
		return $this->all();
	}
	
}