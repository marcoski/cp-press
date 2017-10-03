<?php
namespace Commonhelp\Util\Http;

use Commonhelp\App\Http\Request;

class ServerParams{
	
	/**
	 * 
	 * @var Request
	 */
	private $request;
	
	public function __construct(Request $request = null){
		$this->request = $request;
	}
	
	public function getPostMaxSize(){
		$iniMax = strtolower($this->getNormalizedIniPostMaxSize());
		
		if('' === $iniMax){
			return;
		}
		
		$max = ltrim($iniMax, '+');
		if(0 === strpos($max, '0x')){
			$max = intval($max, 16);
		}else if(0 === strpos($max, '0')){
			$max = intval($max, 8);
		}else{
			$max = (int) $max;
		}
		
		switch(substr($iniMax, -1)){
			case 't': $max *= 1024;
			case 'g': $max *= 1024;
			case 'm': $max *= 1024;
			case 'k': $max *= 1024;
		}
		
		return $max;
	}
	
	public function getNormalizedIniPostMaxSize(){
		return strtoupper(trim(ini_get('post_max_size')));
	}
	
	public function getContentLength(){
		if(null !== $this->request){
			return $this->request->server['CONTENT_LENGTH'];
		}
		
		return isset($_SERVER['CONTENT_LENGTH'])
			? (int) $_SERVER['CONTENT_LENGTH']
			: null;
	}
	
}