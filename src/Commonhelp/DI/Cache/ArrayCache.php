<?php
namespace Commonhelp\DI\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FlushableCache;
use Doctrine\Common\Cache\ClearableCache;

class ArrayCache implements Cache, FlushableCache, ClearableCache{
	
	private $data = array();
	
	public function fetch($id){
		return $this->contains($id) ? $this->data[$id] : null;
	}
	
	public function contains($id){
		return isset($this->data[$id]) || array_key_exists($id, $this->data);	
	}
	
	public function save($id, $data, $lifeTime = 0){
		$this->data[$id] = $data;
		return true;
	}
	
	public function delete($id){
		unset($this->data[$id]);
		return true;
	}
	
	public function getStats(){
		return null;
	}
	
	public function flushAll(){
		$this->data = array();
		
		return true;
	}
	
	public function deleteAll(){
		return $this->flushAll();
	}
}