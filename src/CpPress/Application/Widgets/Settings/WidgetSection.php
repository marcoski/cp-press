<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets\Settings;


use CpPress\Application\WP\Admin\SettingsField\SettingsFieldFactoryInterface;
use CpPress\Application\WP\Admin\SettingsSection\Section\BaseSection;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class WidgetSection extends BaseSection
{

    public function __construct(SettingsFieldFactoryInterface $settingsFieldFactory, SettingsSectionInterface $parent = null)
    {
        parent::__construct($settingsFieldFactory, $parent);
        $this->silent = true;
    }

    public function render(){

    }
}