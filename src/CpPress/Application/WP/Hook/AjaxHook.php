<?php
namespace CpPress\Application\WP\Hook;

use Closure;
use CpPress\Application\CpPressApplication;

class AjaxHook extends Hook{
	
	public function __construct(CpPressApplication $app){
		parent::__construct($app);
	}
	
	public function register($hook, Closure $closure, $priority=10, $acceptedArgs = 1){
		$hook = 'wp_ajax_'.$hook;
		parent::register($hook, $closure, $priority, $acceptedArgs);
	}
	
	public function registerNoPriv($hook, Closure $closure, $priority=10, $acceptedArgs = 1){
		$hook = 'wp_ajax_nopriv_' . $hook;
		parent::register($hook, $closure, $priority, $acceptedArgs);
	}
	
	public function registerFrontEnd($hook, Closure $closure, $priority=10, $acceptedArgs = 1){
		$this->registerNoPriv($hook, $closure, $priority, $acceptedArgs);
		$this->register($hook, $closure, $priority, $acceptedArgs);
	}
	
	public function exec($hook){
		if(!preg_match('/^wp_ajax_*/', $hook)){
			$hook = 'wp_ajax_'.$hook;
		}
		parent::exec($hook);
	}
	
	public function massRegister(){
	}
}