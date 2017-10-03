<?php
namespace Commonhelp\App\Http\Session\Storage\Handler;

class MemcacheSessionHandler implements \SessionHandlerInterface{
	
	private $memcache;
	
	private $ttl;
	
	private $prefix;
	
	public function __construct(\Memcache $memcache, array $options = array()){
		if($diff = array_diff(array_keys($options), array('prefix', 'expiretime'))){
			throw new \InvalidArgumentException(sprintf(
				'The following options are not supported "%s"', implode(', ', $diff)
			));
		}
		
		$this->memcache = $memcache;
		$this->ttl = isset($options['expiretime']) ? (int) $options['expiretime'] : 86400;
		$this->prefix = isset($options['prefix']) ? $options['prefix'] : 'chs';
	}
	
	public function open($savePath, $sessionName){
		return true;
	}
	
	public function close(){
		$this->memcache->close();
	}
	
	public function read($sessionId){
		return $this->memcache->get($this->prefix.$sessionId) ?: '';
	}
	
	public function write($sessionId, $data){
		return $this->memcache->set($this->prefix.$sessionId, $data, 0, time() + $this->ttl);
	}
	
	public function destroy($sessionId){
		return $this->memcache->delete($this->prefix.$sessionId);
	}
	
	public function gc($maxlifetime){
		return true;
	}
	
	public function getMemcache(){
		return $this->memcache;
	}
}