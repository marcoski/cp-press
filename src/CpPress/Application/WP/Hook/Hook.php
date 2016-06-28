<?php
namespace CpPress\Application\WP\Hook;

use Closure;
use CpPress\Application\CpPressApplication;

abstract class Hook{
	
	protected $registered;
	protected $app;
	
	public function __construct(CpPressApplication $app){
		$this->app = $app;
		$this->registered = array();
	}
	
	public function register($hook, Closure $closure, $priority=10, $acceptedArgs = 1){
		$this->registered[$hook][] = array(
			$closure,
			$priority,
			$acceptedArgs
		);
	}
	
	public function exec($hook, $flush=true){
		foreach($this->registered[$hook] as $hookInfo){
			list($closure, $priority, $acceptedArgs) = $hookInfo;
			add_action($hook, $closure, $priority, $acceptedArgs);
		}
		if($flush){
			$this->flush($hook);
		}
	}
	
	public function execAll($flush=true){
		foreach($this->registered as $hookName => $hooks){
			$this->exec($hookName, $flush);
		}
		if($flush){
			$this->flush();
		}
	}
	
	public function create(){
		$args = func_get_args();
		call_user_func_array('do_action', $args);
	}
	
	public function flush($hook=null){
		if(is_null($hook)){
			$this->registered = array();
		}else{
			unset($this->registered[$hook]);
		}
	}
	
	abstract public function massRegister();
}