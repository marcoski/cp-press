<?php
namespace CpPress\Application\Login\Provider;

use CpPress\Application\Login\LoginProviderInterface;
use CpPress\Application\Login\UserProviderInterface;
use CpPress\Application\Login\UserDataInterface;
use CpPress\Application\Login\UserData;

abstract class BaseProvider implements LoginProviderInterface, UserProviderInterface{
	
	private $options;
	
	/**
	 * @var UserDataInterface
	 */
	protected $userData;
	
	
	public function __construct(array $options){
		$this->options = $options;
		$this->userData = new UserData(array(
			'user_pass' => md5( microtime() ),
			'user_login' => '',
			'user_nicename' => '',
			'user_email' => '',
			'display_name' => '',
			'first_name' => '',
			'last_name' => '',
			'user_url' => '',
			'role' => ''
		));
		if($this->has('userdefaultrole') && $this->get('userdefaultrole') !== ''){
			$this->userData->set('role', $this->get('userdefaultrole'));
		}
	}
	
	public function all(){
		return $this->options;
	}
	
	public function get($name){
		if(isset($this->options[$name])){
			return $this->options[$name];
		}
		
		return null;
	}
	
	public function has($name){
		return isset($this->options[$name]);
	}
	
	public function true($name){
		return $this->has($name) && (int) $this->get($name) === 1;
	}
	
	public function error($code, $message){
		remove_all_filters('authenticate');
		return new \WP_Error($code, $message);
	}
	
	abstract public function authenticate($username, $password);
	
	abstract public function loadUserByUsername($username);
	
}