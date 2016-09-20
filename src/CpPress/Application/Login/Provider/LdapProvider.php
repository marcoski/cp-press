<?php
namespace CpPress\Application\Login\Provider;

use Symfony\Component\Ldap\LdapClient;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;

class LdapProvider extends BaseProvider{
	
	/**
	 * @var LdapClient;
	 */
	private $ldap;
	
	private $dnUser;
	private $dnUserString;
	private $dnGroup;
	private $dnGroupString;
	
	private $filterUserGroup;
	
	private $displayGroup;
	
	private $adminGroups = array('Administrators', 'Website Admin');
	
	
	public function __construct(array $options){
		parent::__construct($options);
		$this->ldap = $this->connect();
		
		$this->dnUser = 'ou=Users,'.$this->get('basedn');
		if($this->has('userbasedn') && $this->get('userbasedn') !== ''){
			$this->dnUser = $this->get('userbasedn');
		}
		
		$this->dnUserString = 'uid={username}';
		if($this->has('login') && $this->get('login') !== ''){
			$this->dnUserString = $this->get('login');
		}
		$this->dnGroup = 'ou=Gourps,'.$this->get('basedn');
		if($this->has('groupbasedn') && $this->get('groupbasedn') !== ''){
			$this->dnGroup = $this->get('groupbasedn');
		}
		
		$this->dnGroupString = 'cn={group}';
		if($this->has('group') && $this->get('group') !== ''){
			$this->dnGroupString = $this->get('group');
		}
		
		$this->filterUserGroup = '(memberUid={username})';
		if($this->has('filterusergroup') && $this->get('filterusergroup') !== ''){
			$this->filterUserGroup = $this->get('filterusergroup');
		}
		
		$this->displayGroup = 'cn';
		if($this->has('displaygroup') && $this->get('displaygroup') !== ''){
			$this->displayGroup = $this->get('displaygroup');
		}
	}
	
	public function authenticate($username, $password){
		if('' === $password || '' === $username){
			$error = new \WP_Error();
			if('' === $username){
				$error->add('empty_username', __('<strong>ERROR:</strong>: The username field is empty.'));
			}
			if('' === $password){
				$error->add('empty_password', __('<strong>ERROR:</strong>: The password field is empty'));
			}
			
			return $error;
		}
		try{
			$username = $this->ldap->escape($username, '', LdapInterface::ESCAPE_DN);
			$dn = str_replace('{username}', $username, $this->dnUserString).','.$this->dnUser;
			
			$this->ldap->bind($dn, $password);
		}catch(ConnectionException $e){
			return false;
		}
		
		return true;
	}
	
	public function loadUserByUsername($username){
		try{
			$username = $this->ldap->escape($username, '', LdapInterface::ESCAPE_FILTER);
			$this->userHasGroup($username);
			$query = '('.str_replace('{username}', $username, $this->dnUserString).')';
			$search = $this->ldap->query($this->get('basedn'), $query);
		}catch(ConnectionException $e){
			return $this->error('username_not_found', sprintf(__('<strong>ERROR:</strong>: User "%s" not found', 'cppress'), $username));
		}
		
		$entries = $search->execute();
		$count = count($entries);
		
		if(!$count){
			return $this->error('username_not_found', sprintf(__('<strong>ERROR:</strong>: User "%s" not found', 'cppress'), $username));
		}
		
		if($count > 1){
			return $this->error('more_than_one_user_found', __('<strong>ERROR:</strong>: More than one user found'));
		}
		return $this->loadUser($username, $entries[0]);
	}
	
	private function connect(){
		$useSsl = false;
		$hostDc = $this->get('dc');
		$portDc = $this->get('dcport') !== null ? $this->get('dcport') : '389';
		$versionDc = $this->get('version') !== null ? $this->get('version') : '3';
		if(preg_match("/(ldap|ldaps):\/\/(\S*)/", $hostDc, $match)){
			if($match[1] === 'ldaps'){
				$useSsl = true;
			}
			$hostDc = $match[2];
		}
		
		$ldap = new LdapClient($hostDc, $portDc, $versionDc, $useSsl);
		$ldap->bind($this->get('searchdn'), $this->get('searchpassword'));
		return $ldap;
	}
	
	private function loadUser($username, Entry $entry){
		$user = get_user_by('login', $username);
		if(false === $user || strtolower($user->user_login) !== strtolower($username)){
			if(!$this->true('usercreations')){
				return $this->error('invalid_username', __('<strong>Simple LDAP Login Error</strong>: LDAP credentials are correct, but there is no matching WordPress user and user creation is not enabled.'));
			}
			
			$this->userData->set('user_login', $username);
			$this->userData->set('user_nicename',
				$this->getUserData($entry, $this->get('userfirstnameattribute')).' - '.
				$this->getUserData($entry, $this->get('userlastnameattribute')));
			$this->userData->set('user_email', $this->getUserData($entry, $this->get('useremailattribute')));
			$this->userData->set('display_name',
					$this->getUserData($entry, $this->get('userfirstnameattribute')) .' '.
					$this->getUserData($entry, $this->get('userlastnameattribute')));
			$this->userData->set('first_name', $this->getUserData($entry, $this->get('userfirstnameattribute')));
			$this->userData->set('last_name', $this->getUserData($entry, $this->get('userlastnameattribute')));
			$this->userData->set('user_url', $this->getUserData($entry, $this->get('userurlattribute')));
			
			$newUser = wp_insert_user($this->userData);
			
			if($newUser instanceof \WP_Error){
				return $this->error('login_error', '<strong>ERROR</strong>: '.$newUser->get_error_message());
			}
			
			foreach($this->getUserMetaData($entry) as $metaKey => $metaValue){
				add_user_meta($newUser, $metaKey, $metaValue);
			}
			
			return new \WP_User($newUser);
			
		}else{
			return new \WP_User($user->ID);
		}
	}
	
	private function userHasGroup($username){
		$groups = $this->has('groups') ? (array) $this->get('groups') : array();
		$groups = array_filter($groups);
		
		if(count($groups) === 0){
			return;
		}
		
		$query = str_replace('{username}', $username, $this->filterUserGroup);
		$search = $this->ldap->query($this->get('basedn'), $query);
		$entries = $search->execute();
		$count = count($entries);
		
		if($count > 0){
			$userGroups = array();
			foreach($entries as $entry){
				$groupName = $entry->getAttribute($this->displayGroup);
				$userGroups[] = is_array($groupName) ? $groupName[0] : $groupName;	
			}
			
			$validGroups = array_intersect($userGroups, $groups);
			foreach($this->adminGroups as $adminGroup){
				if(in_array($adminGroup, $validGroups)){
					$this->userData->set('role', 'administrator');
					break;
				}
			}
			
			if(count($validGroups) > 0){
				return;
			}
		}
		
		throw new ConnectionException('No groups found');
	}
	
	private function getUserData(Entry $entry, $attribute){
		$value = $entry->getAttribute(strtolower($attribute));
		return is_array($value) ? $value[0] : $value;
	}
	
	private function getUserMetaData(Entry $entry){
		$settingsUserMetaData = $this->has('usermetadata') ? (array) $this->get('usermetadata') : array();
		$userMetaData = array();
		
		foreach($settingsUserMetaData as $attribute){
			$userMetaData[$attribute[1]] = $this->getUserData($entry, $attribute[0]);
		}
		
		return $userMetaData;
	}
}