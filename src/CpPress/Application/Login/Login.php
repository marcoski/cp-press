<?php
namespace CpPress\Application\Login;

use CpPress\Application\Login\Provider\LdapProvider;

class Login implements LoginInterface{
	
	/**
	 * @var LoginProviderInterface[];
	 */
	private $providers;
	
	public function __construct(){
		$this->providers = array();
		$ldapOptions = get_option('cppress-options-ldap');
		if(isset($ldapOptions['enabled']) && (int) $ldapOptions['enabled'] === 1){
			unset($ldapOptions['enabled']);
			$this->providers[] = new LdapProvider($ldapOptions);
		}
	}
	
	public function authenticate($user, $username, $password){
		foreach($this->providers as $provider){
			$result = $provider->authenticate($username, $password);
			if($result instanceof \WP_Error){
				return $result;
			}
			
			if(false === $result){
				return null;
			}
			
			return $provider->loadUserByUsername($username);
		}
	}
	
}

