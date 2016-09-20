<?php
namespace CpPress\Application\Login;

interface LoginProviderInterface{
	
	public function authenticate($username, $password);
	
	public function get($name);
	public function has($name);
	public function all();
	public function true($name);
	
	public function error($code, $message);
	
}