<?php
namespace CpPress\Application\WP\Hook;

use CpPress\Application\CpPressApplication;

class Filter extends Hook{
	
	public function __construct(CpPressApplication $app){
		parent::__construct($app);
	}
	
	public function massRegister(){
	}
	
	public function exec($hook, $flush=true){
		foreach($this->registered[$hook] as $hookInfo){
			list($closure, $priority, $acceptedArgs) = $hookInfo;
			add_filter($hook, $closure, $priority, $acceptedArgs);
		}
		if($flush){
			$this->flush($hook);
		}
	}
	
	public function apply(){
		$args = func_get_args();
		return call_user_func_array('apply_filters', $args);
	}
	
}