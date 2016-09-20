<?php
namespace CpPress\Application\Login;

interface UserProviderInterface{
	
	public function loadUserByUsername($username);
	
}