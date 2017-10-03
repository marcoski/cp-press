<?php
namespace CpPress\Application\WP\Hook;


use CpPress\Application\CpPressApplication;
use CpPress\Application\Login\LoginInterface;
use CpPress\Application\Login\Login;

class LoginFilter extends Filter{
	
	private $login;
	
	public function __construct(CpPressApplication $app, LoginInterface $login = null){
		parent::__construct($app);
		$this->login = $login !== null ?: new Login();
	}

	public function massRegister(){
		$this->register('authenticate', array($this->login, 'authenticate'), 1, 3);
	}
	
}