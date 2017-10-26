<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets\Settings;


use CpPress\Application\WP\Admin\SettingsField\Field\AccordionElementField;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class CpWidgetButtonSettings extends AccordionElementField
{

    public function __construct(SettingsSectionInterface $section)
    {
        $this
            ->setId('cp-widget-button-settings')
            ->setTitle(__('Button Widget Settings', 'cppress'))
            ->setSection($section)
            ->setName('cp-widget-button-settings');
    }

    public function render(array $args)
    {
        $this
            ->renderAccordionTitle()
            ->renderAccordionElementOpen()
            ->renderAccordionElementClose();
    }
}