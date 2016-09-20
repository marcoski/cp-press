<?php
namespace CpPress\Application\WP\Admin\SettingsSection;

use CpPress\Application\WP\Admin\SettingsSection\Section\GeneralSection;
use CpPress\Application\WP\Admin\SettingsSection\Section\AttachmentSection;
use CpPress\Exception\SettingsException;
use CpPress\Application\WP\Admin\SettingsField\GeneralSettingsFieldFactory;
use CpPress\Application\WP\Admin\SettingsField\AttachmentSettingsFieldFactory;
use CpPress\Application\WP\Admin\SettingsSection\Section\LdapSection;
use CpPress\Application\WP\Admin\SettingsField\LdapSettingsFieldFactory;
use CpPress\Application\WP\Admin\SettingsSection\Section\AdvancedLdapSection;
use CpPress\Application\WP\Admin\SettingsField\AdvancedLdapSettingsFieldFactory;
use CpPress\Application\WP\Admin\SettingsSection\Section\UsermapLdapSection;
use CpPress\Application\WP\Admin\SettingsField\UsermapLdapSettingsFieldFactory;

class SettingsSectionFactory implements SettingsSectionFactoryInterface{
	
	private $sections;
	
	public function create(){
		$generalSection = new GeneralSection(new GeneralSettingsFieldFactory());
		$attachmentSection = new AttachmentSection(new AttachmentSettingsFieldFactory());
		
		$this->sections = array(
			'cppress-options-general' => 
				$generalSection->setId('cppress-options-general')
				->setTitle(__('General Settings', 'cppress'))
				->setPage('cppress-options-general'),
			'cppress-options-attachment' =>
				$attachmentSection->setId('cppress-options-attachment')
				->setTitle(__('Attachment Settings', 'cppress'))
				->setPage('cppress-options-attachment'),
			'cppress-options-ldap' => $this->createLdapSection()
		);
		
		$this->add();
	}
	
	public function render($section){
		if(!$this->has($section)){
			throw new SettingsException(sprintf('No section "%s" registered', $section));
		}
		do_settings_sections($section);
		foreach($this->get($section) as $childSection){
			do_settings_sections($childSection->getId());
		}
	}
	
	public function get($section){
		if($this->has($section)){
			return $this->sections[$section];
		}
		
		return null;
	}
	
	public function has($section){
		return isset($this->sections[$section]);
	}
	
	public function all(){
		return $this->sections;
	}
	
	private function add(){
		foreach($this->sections as $section){
			$section->addSection();
			if(count($section > 0)){
				$this->addSubSections($section);
			}
		}
	}
	
	private function addSubSections(SettingsSectionInterface $parent){
		foreach($parent as $child){
			$child->addSection();
			$this->addSubSections($child);
		}
	}
	
	private function createLdapSection(){
		$ldapSection = new LdapSection(new LdapSettingsFieldFactory());
		$ldapSection->setId('cppress-options-ldap')
		->setTitle(__('Ldap Settings', 'cppress'))
		->setPage('cppress-options-ldap');
		
		$advancedLdapSection = new AdvancedLdapSection(new AdvancedLdapSettingsFieldFactory());
		$advancedLdapSection->setId('cppress-options-ldap-advanced')
		->setTitle(__('Advanced Settings', 'cppress'));
		$ldapSection->add($advancedLdapSection);
		
		$userMapLdapSection = new UsermapLdapSection(new UsermapLdapSettingsFieldFactory());
		$userMapLdapSection->setId('cppress-options-ldap-usermap')
		->setTitle(__('LDAP User Map to Wordpress', 'cppress'));
		$ldapSection->add($userMapLdapSection);
		
		return $ldapSection;
	}
	
}