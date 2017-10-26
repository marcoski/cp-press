<?php
namespace CpPress\Application\WP\Admin\SettingsSection\Section;

use CpPress\Application\WP\Admin\SettingsField\SettingsFieldFactoryInterface;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class GeneralSection extends BaseSection
{

    public function __construct(SettingsFieldFactoryInterface $settingsFieldFactory, SettingsSectionInterface $parent = null)
    {
        parent::__construct($settingsFieldFactory, $parent);
        $this->silent = true;
    }

    public function render()
    {
		
	}
	
}