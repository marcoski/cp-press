<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\WP\Admin\SettingsField;


use CpPress\Application\WP\Admin\SettingsField\Field\TextField;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class ApiKeysFieldFactory extends BaseSettingsFieldFactory
{
    public function create(SettingsSectionInterface $section){
        $gKeyField = new TextField();
        $this->fields['google-api-key'] = $gKeyField
            ->setId('google-api-key')
            ->setTitle(__('API Key for google Services', 'cppress'))
            ->setSection($section)
            ->setName('google-api-key');

        $this->add();
    }

}