<?php
namespace CpPress\Application\WP\Admin\SettingsField;

use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;
use CpPress\Application\WP\Admin\SettingsField\Field\TextField;
use CpPress\Application\WP\Admin\SettingsField\Field\RolesField;
use CpPress\Application\WP\Admin\SettingsField\Field\ListTextField;
use CpPress\Application\WP\Admin\SettingsField\Field\ListTextareaField;

class UsermapLdapSettingsFieldFactory extends BaseSettingsFieldFactory{
	
	private $userFields;
	
	public function __construct(){
		$this->userFields = array(
			'userfirstnameattribute' => array(
				'description' => __('The LDAP attribute for the first name', 'cppress'),
				'title' => __('First name', 'cppress')
			),
			'userlastnameattribute' => array(
				'description' => __('The LDAP attribute for the last name', 'cppress'),
				'title' => __('Last name', 'cppress')
			),
			'useremailattribute' => array(
				'description' => __('The LDAP attribute for the email', 'cppress'),
				'title' => __('Email', 'cppress')
			),
			'userurlattribute' => array(
				'description' => __('The LDAP attribute for the website'),
				'title' => __('Website', 'cppress')
			)
		);
	}
	
	public function create(SettingsSectionInterface $section){
		foreach($this->userFields as $field => $description){
			$this->createTextField($section, $field, $description);
		}
		$this->createDropdownRoles($section);
		$this->createAdminGroups($section);
		$this->createUserMeta($section);
		$this->add();
	}
	
	private function createTextField(SettingsSectionInterface $section, $fieldName, $description){
		$field = new TextField();
		$fieldId = 'ldap-'.$fieldName;
		$this->fields[$fieldId] = $field
		->setId($fieldId)
		->setTitle($description['title'])
		->setSection($section)
		->setName($fieldName)
		->setArgs(array('description' => $description['description']));
	}
	
	private function createDropdownRoles(SettingsSectionInterface $section){
		$field = new RolesField();
		$this->fields['ldap-userdefaultrole'] = $field
		->setId('ldap-userdefaultrole')
		->setTitle(__('New User Role', 'cppress'))
		->setSection($section)
		->setName('userdefaultrole');
	}
	
	private function createAdminGroups(SettingsSectionInterface $section){
		$field = new ListTextField();
		$this->fields['ldap-admingroups'] = $field
		->setId('ldap-admingroups')
		->setTitle(__('Mapped Admin Groups', 'cppress'))
		->setSection($section)
		->setName('admingroups')
		->setArgs(array('description' => __('The groups, if any, that map to Administrator Role. Empty means no groups. Separate with commas', 'cppress')));
	}
	
	private function createUserMeta(SettingsSectionInterface $section){
		$description = __('Additional user data can be stored as user meta data. You can specify the LDAP attributes and the associated wordpress meta keys in the format <ldap_attribute_name>:<wordpress_meta_key>. Multiple attributes can be given on separate lines.', 'cppress');
		$field = new ListTextareaField();
		$this->fields['ldap-usermetadata'] = $field
		->setId('ldap-usermetadata')
		->setTitle(__('User Meta Data', 'cppress'))
		->setSection($section)
		->setName('usermetadata')
		->setArgs(array('description' => $description));
	}
	
	
}