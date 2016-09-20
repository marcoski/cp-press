<?php
namespace CpPress\Application\Login;

interface UserDataInterface{
	
	public function get($name);
	public function has($name);
	public function all();
	
	public function set($name, $value);
	public function remove($name);
	
}