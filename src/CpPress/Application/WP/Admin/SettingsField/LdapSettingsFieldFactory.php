<?php
namespace CpPress\Application\WP\Admin\SettingsField;

use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;
use CpPress\Application\WP\Admin\SettingsField\Field\TextField;
use CpPress\Application\WP\Admin\SettingsField\Field\PasswordField;
use CpPress\Application\WP\Admin\SettingsField\Field\BaseDnField;
use CpPress\Application\WP\Admin\SettingsField\Field\EnabledField;
use CpPress\Application\WP\Admin\SettingsField\Field\DomainControllerField;
use CpPress\Application\WP\Admin\SettingsField\Field\NumberField;
use CpPress\Application\WP\Admin\SettingsField\Field\ListTextField;

class LdapSettingsFieldFactory extends BaseSettingsFieldFactory{
	
	public function create(SettingsSectionInterface $section){
		$this->createLdapEnabled($section);
		$this->createDomainController($section);
		$this->createDcPort($section);
		$this->createBaseDnField($section);
		$this->createSearchDnField($section);
		$this->createSearchPassword($section);
		$this->createRequiredGroups($section);
		$this->createLdapOnly($section);
		$this->createUserCreations($section);
		$this->add();
	}
	
	private function createLdapEnabled(SettingsSectionInterface $section){
		$enabledField = new EnabledField();
		$this->fields['ldap-enabled'] = $enabledField
		->setId('ldap-enabled')
		->setTitle(__('Enable LDAP Authentication', 'cppress'))
		->setSection($section)
		->setName('enabled');
	}
	
	private function createBaseDnField(SettingsSectionInterface $section){
		$baseDnField = new BaseDnField();
		$this->fields['ldap-basedn'] = $baseDnField
		->setId('ldap-basedn')
		->setTitle(__('Base DN for the directory', 'cppress'))
		->setSection($section)
		->setName('basedn');
	}
	
	private function createSearchDnField(SettingsSectionInterface $section){
		$searchDnField = new TextField();
		$this->fields['ldap-searchdn'] = $searchDnField
		->setId('ldap-searchdn')
		->setTitle(__('Search DN for the directory', 'cppress'))
		->setSection($section)
		->setName('searchdn');
	}
	
	private function createSearchPassword(SettingsSectionInterface $section){
		$searchDnField = new PasswordField();
		$this->fields['ldap-searchpassword'] = $searchDnField
		->setId('ldap-searchpassword')
		->setTitle(__('Search DN user password', 'cppress'))
		->setSection($section)
		->setName('searchpassword');
	}
	
	private function createDomainController(SettingsSectionInterface $section){
		$dcField = new DomainControllerField();
		$this->fields['ldap-dc'] = $dcField
		->setId('ldap-dc')
		->setTitle(__('Domain controller', 'cppress'))
		->setSection($section)
		->setName('dc');
	}
	
	private function createDcPort(SettingsSectionInterface $section){
		$dcPortField = new NumberField();
		$this->fields['ldap-dcport'] = $dcPortField
		->setId('ldap-dcport')
		->setTitle(__('Domain controller port', 'cppress'))
		->setSection($section)
		->setName('dcport');
	}
	
	private function createLdapOnly(SettingsSectionInterface $section){
		$enabledField = new EnabledField();
		$this->fields['ldap-highsecurity'] = $enabledField
		->setId('ldap-highsecurity')
		->setTitle(__('LDAP only', 'cppress'))
		->setSection($section)
		->setName('highsecurity')
		->setArgs(array('description' => __('Force all logins to authenticate against LDAP. Do NOT fallback to default authentication for existing users.', 'cppress')));
	}
	
	private function createUserCreations(SettingsSectionInterface $section){
		$enabledField = new EnabledField();
		$this->fields['ldap-usercreations'] = $enabledField
		->setId('ldap-usercreations')
		->setTitle(__('User creations', 'cppress'))
		->setSection($section)
		->setName('usercreations')
		->setArgs(array('description' => __('Create Wordpress user for authenticated LDAP login whit appropriate roles', 'cppress')));
	}
	
	private function createRequiredGroups(SettingsSectionInterface $section){
		$searchDnField = new ListTextField();
		$this->fields['ldap-groups'] = $searchDnField
		->setId('ldap-groups')
		->setTitle(__('Required groups', 'cppress'))
		->setSection($section)
		->setName('groups')
		->setArgs(array('description' => __('The groups, if any, that authenticating LDAP user must belong to. Empty means no groups. Separate with commas', 'cppress')));
	}
}