<?php
namespace CpPress\Application\Login;

interface LoginInterface{
	
	public function authenticate($user, $username, $password);
	
}