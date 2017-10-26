<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets\Settings;


use CpPress\Application\WP\Admin\SettingsField\Field\AccordionElementField;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class CpWidgetFacebookSettings extends AccordionElementField
{
    public function __construct(SettingsSectionInterface $section)
    {
        $this
            ->setId('cp-widget-facebook-settings')
            ->setTitle(__('Facebook Widget Settings', 'cppress'))
            ->setSection($section)
            ->setName('cp-widget-facebook-settings');
    }

    public function render(array $args)
    {
        $this
            ->renderAccordionTitle()
            ->renderAccordionElementOpen()
            ->renderAccordionElementClose();
    }
}