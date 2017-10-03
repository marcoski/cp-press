<?php
namespace Commonhelp\App\Http\Session\Attribute;

use Commonhelp\App\Http\Session\Flash\FlashBagInterface;

class AutoExpireFlashBag implements FlashBagInterface{
	
	private $name = 'flashes';
	
	private $flashes = array('display' => array(), 'new' => array());
	
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
		$this->flashes['display'] = array_key_exists('new', $this->flashes) ? $this->flashes['new'] : array();
		$this->flashes['new'] = array();
	}
	
	public function add($type, $message){
		$this->flashes['new'][$type][] = $message;
	}
	
	public function peek($type, array $default = array()){
		return $this->has($type) ? $this->flashes['display'][$type] : $default;
	}
	
	public function peekAll(){
		return array_key_exists('display', $this->flashes) ? (array) $this->flashes['display'] : array();
	}
	
	public function get($type, array $default = array()){
		$return = $default;
		
		if(!$this->has($type)){
			return $return;
		}
		
		if(isset($this->flashes['display'][$type])){
			$return = $this->flashes['display'][$type];
			unset($this->flashes['display'][$type]);
		}
		
		return $return;
	}
	
	public function all(){
		$return = $this->flashes['display'];
		$this->flashes = array('new' => array(), 'display' => array());
		
		return $return;
	}
	
	public function setAll(array $messages){
		$this->flashes['new'] = $messages;
	}
	
	public function set($type, $message){
		$this->flashes['new'][$type] = (array) $message;
	}
	
	public function has($type){
		return array_key_exists($type, $this->flashes['display']) && $this->flashes['display'][$type];
	}
	
	public function keys(){
		return array_keys($this->flashes['display']);
	}
	
	public function getStorageKey(){
		return $this->storageKey;
	}
	
	public function clear(){
		return $this->all();
	}
	
}