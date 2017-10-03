<?php
namespace Commonhelp\App\Http\Session\Storage;

use Commonhelp\App\Http\Session\SessionBagInterface;

class MetadataBag implements SessionBagInterface{
	
	const CREATED = 'c';
	const UPDATED = 'u';
	const LIFETIME = 'l';

	private $name = '__metadata';
	
	private $storageKey;
	
	protected $meta = array(self::CREATED => 0, self::UPDATED => 0, self::LIFETIME => 0);
	
	private $lastUsed;
	
	private $updateThershold;
	
	public function __construct($storageKey = '_ch_meta', $updateThershold = 0){
		$this->storageKey = $storageKey;
		$this->updateThershold = $updateThershold;
	}
	
	public function initialize(array &$array){
		$this->meta = &$array;
		
		if(isset($array[self::CREATED])){
			$this->lastUsed = $this->meta[self::UPDATED];
			$timeStamp = time();
			if($timeStamp - $array[self::UPDATED] >= $this->updateThershold){
				$this->meta[self::UPDATED] = $timeStamp;
			}
		}else{
			$this->stampCreated();
		}
	}
	
	public function getLifetime(){
		return $this->meta[self::LIFETIME];
	}
	
	public function stampNew($lifetime = null){
		$this->stampCreated($lifetime);
	}
	
	public function getStorageKey(){
		return $this->storageKey;
	}
	
	public function getCreated(){
		return $this->meta[self::CREATED];
	}
	
	public function getLastUsed(){
		return $this->lastUsed;
	}
	
	public function clear(){
		
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	private function stampCreated($lifetime = null){
		$timestamp = time();
		$this->meta[self::CREATED] = $this->meta[self::UPDATED] = $this->lastUsed = $timestamp;
		$this->meta[self::LIFETIME] = (null === $lifetime) ? ini_get('session.cookie_lifetime') : $lifetime;
	}
}