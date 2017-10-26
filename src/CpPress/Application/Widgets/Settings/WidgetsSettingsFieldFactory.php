<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets\Settings;


use CpPress\Application\WP\Admin\SettingsField\BaseSettingsFieldFactory;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class WidgetsSettingsFieldFactory extends BaseSettingsFieldFactory
{
    public function create(SettingsSectionInterface $section)
    {
        $cpWidgetSettings = new CpWidgetSettings($section);
        $this->fields['cp-widget-settings'] =$cpWidgetSettings
            ->setId('cp-widget-settings')
            ->setTitle(__('Widget Settings', 'cppress'))
            ->setSection($section)
            ->setName('cp-widget-settings');
        $this->add();
    }

}