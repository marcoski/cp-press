<?php
namespace CpPress\Application\WP\Admin\SettingsField;

use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class GeneralSettingsFieldFactory extends BaseSettingsFieldFactory{
	
	public function create(SettingsSectionInterface $section){
		$this->add();
	}
	
}