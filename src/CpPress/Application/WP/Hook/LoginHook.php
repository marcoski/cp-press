<?php
namespace CpPress\Application\WP\Hook;

use CpPress\Application\CpPressApplication;

class LoginHook extends Hook{
	
	public function __construct(CpPressApplication $app){
		parent::__construct($app);
	}

	public function massRegister(){
		
	}
	
}