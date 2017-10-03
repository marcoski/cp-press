<?php
namespace Commonhelp\App\Http\Session\Storage\Handler;

class WriteCheckSessionHandler implements \SessionHandlerInterface{
	
	/**
	 * 
	 * @var \SessionHandlerInterface
	 */
	private $wrappedSessionHandler;
	
	private $readSessions;
	
	public function __construct(\SessionHandlerInterface $wrappedSessionHandler){
		$this->wrappedSessionHandler = $wrappedSessionHandler;
	}
	
	public function close(){
		return $this->wrappedSessionHandler->close();
	}
	
	public function destroy($sessionId){
		return $this->wrappedSessionHandler->destroy($sessionId);
	}
	
	public function gc($maxlifetime){
		return $this->wrappedSessionHandler->gc($maxlifetime);
	}
	
	public function open($savePath, $sessionName){
		return $this->wrappedSessionHandler->open($savePath, $sessionName);
	}
	
	public function read($sessionId){
		$session = $this->wrappedSessionHandler->read($sessionId);
		$this->readSessions[$sessionId] = $session;
		
		return $session;
	}
	
	public function write($sessionId, $data){
		if(isset($this->readSessions[$sessionId]) && $data === $this->readSessions[$sessionId]){
			return true;
		}
		
		return $this->wrappedSessionHandler->write($sessionId, $data);
	}
	
}