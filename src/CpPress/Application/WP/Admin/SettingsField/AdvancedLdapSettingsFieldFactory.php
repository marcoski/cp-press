<?php
namespace CpPress\Application\WP\Admin\SettingsField;

use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;
use CpPress\Application\WP\Admin\SettingsField\Field\BaseDnField;
use CpPress\Application\WP\Admin\SettingsField\Field\TextField;
use CpPress\Application\WP\Admin\SettingsField\Field\EnabledField;

class AdvancedLdapSettingsFieldFactory extends BaseSettingsFieldFactory{
	
	public function create(SettingsSectionInterface $section){
		$this->createUserBaseDn($section);
		$this->createGroupBaseDn($section);
		$this->createLoginAttribute($section);
		$this->createGroupAttribute($section);
		$this->createFilterUserGroupAttribute($section);
		$this->createDisplayGroupAttribute($section);
		$this->createUseTls($section);
		$this->createLdapVersion($section);
		$this->add();
	}
	
	private function createUserBaseDn(SettingsSectionInterface $section){
		$field = new BaseDnField();
		$this->fields['ldap-userbasedn'] = $field
		->setId('ldap-userbasedn')
		->setTitle(__('User Base DN (optional)', 'cppress'))
		->setSection($section)
		->setName('userbasedn')
		->setArgs(array('description' => __('If you need to specify a different Base DN for user searches. Example: For subdomain.domain.suffix, use ou=users,DC=subdomain,DC=domain,DC=suffix.', 'cppress')));
	}
	
	private function createGroupBaseDn(SettingsSectionInterface $section){
		$field = new BaseDnField();
		$this->fields['ldap-groupbasedn'] = $field
		->setId('ldap-groupbasedn')
		->setTitle(__('Group Base DN (optional)', 'cppress'))
		->setSection($section)
		->setName('groupbasedn')
		->setArgs(array('description' => __('If you need to specify a different Base DN for group searches. Example: For subdomain.domain.suffix, use ou=groups,DC=subdomain,DC=domain,DC=suffix.', 'cppress')));
	}
	
	private function createLoginAttribute(SettingsSectionInterface $section){
		$field = new TextField();
		$this->fields['ldap-login'] = $field
		->setId('ldap-login')
		->setTitle(__('LDAP Login Attribute', 'cppress'))
		->setSection($section)
		->setName('login')
		->setArgs(array('description' => __('Default <strong>uid={username}</strong> use {username} placeholder', 'cppress')));
	}
	
	private function createFilterUserGroupAttribute(SettingsSectionInterface $section){
		$field = new TextField();
		$this->fields['ldap-filterusergroup'] = $field
		->setId('ldap-filterusergroup')
		->setTitle(__('LDAP Filter User Group', 'cppress'))
		->setSection($section)
		->setName('filterusergroup')
		->setArgs(array('description' => __('Default <strong>(memberUid={username})</strong> use {username} placeholder', 'cppress')));
	}
	
	private function createGroupAttribute(SettingsSectionInterface $section){
		$field = new TextField();
		$this->fields['ldap-group'] = $field
		->setId('ldap-group')
		->setTitle(__('LDAP Group Attribute', 'cppress'))
		->setSection($section)
		->setName('group')
		->setArgs(array('description' => __('Default <strong>cn={username}</strong> use {username} placeholder', 'cppress')));
	}
	
	private function createDisplayGroupAttribute(SettingsSectionInterface $section){
		$field = new TextField();
		$this->fields['ldap-displaygroup'] = $field
		->setId('ldap-displaygroup')
		->setTitle(__('LDAP Display Group Attribute', 'cppress'))
		->setSection($section)
		->setName('displaygroup')
		->setArgs(array('description' => __('In case your installation uses something other than cn;', 'cppress')));
	}
	
	private function createUseTls(SettingsSectionInterface $section){
		$field = new EnabledField();
		$this->fields['ldap-usetls'] = $field
		->setId('ldap-usetls')
		->setTitle(__('Use TLS', 'cppress'))
		->setSection($section)
		->setName('usetls');
	}
	
	private function createLdapVersion(SettingsSectionInterface $section){
		$field = new TextField();
		$this->fields['ldap-version'] = $field
		->setId('ldap-version')
		->setTitle(__('LDAP Version', 'cppress'))
		->setSection($section)
		->setName('version')
		->setArgs(array('description' => __('Typically 3', 'cppress')));
	}
	
}