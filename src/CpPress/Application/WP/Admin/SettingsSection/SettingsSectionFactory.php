<?php
namespace CpPress\Application\WP\Admin\SettingsSection;

use CpPress\Application\Widgets\Settings\WidgetSection;
use CpPress\Application\Widgets\Settings\WidgetsSettingsFieldFactory;
use CpPress\Application\WP\Admin\SettingsField\ApiKeysFieldFactory;
use CpPress\Application\WP\Admin\SettingsField\Field\BaseField;
use CpPress\Application\WP\Admin\SettingsSection\Section\ApiKeysSection;
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
		$attachmentSection = new AttachmentSection(new AttachmentSettingsFieldFactory());
		$apiKeySection = new ApiKeysSection(new ApiKeysFieldFactory());
		
		$this->sections = array(
			'cppress-options-general' => $this->createGeneralSection(),
			'cppress-options-attachment' =>
				$attachmentSection->setId('cppress-options-attachment')
				->setTitle(__('Attachment Settings', 'cppress'))
				->setPage('cppress-options-attachment'),
			'cppress-options-ldap' => $this->createLdapSection(),
            'cppress-options-apikey' =>
                $apiKeySection->setId('cppress-options-apikey')
                    ->setTitle(__('Api Keys Settings', 'cppress'))
                    ->setPage('cppress-options-apikey')

		);
		
		$this->add();
	}
	
	public function render($section){
		if(!($section instanceof SettingsSectionInterface) && !$this->has($section)){
			throw new SettingsException(sprintf('No section "%s" registered', $section));
		}
		if(!($section instanceof SettingsSectionInterface)){
		    $section = $this->get($section);
        }
        if(!$section->isSilent()){
            do_settings_sections($section->getId());
        }else{
            /** @var BaseField $field */
            if($section->getSettingsFieldFactory()->all() !== null){
                /** @var BaseField $field */
                foreach($section->getSettingsFieldFactory()->all() as $field){
                    $field->render($field->all());
                }
            }else{
                /** @var SettingsSectionInterface $child */
                foreach($section as $child){
                    $this->render($child);
                }
            }
        }

	}

    /**
     * @param string $section
     * @return SettingsSectionInterface | null
     */
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
			if(count($section) > 0){
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

	private function createGeneralSection(){
        $generalSection = new GeneralSection(new GeneralSettingsFieldFactory());
        $generalSection->setId('cppress-options-general')
            ->setTitle(__('General Settings', 'cppress'))
            ->setPage('cppress-options-general');

        $widgetsSection = new WidgetSection(new WidgetsSettingsFieldFactory());
        $widgetsSection->setId('cppress-options-widgets')
            ->setTitle(__('Widgets Settings', 'cppress'));
        $generalSection->add($widgetsSection);

        return $generalSection;
    }
	
}