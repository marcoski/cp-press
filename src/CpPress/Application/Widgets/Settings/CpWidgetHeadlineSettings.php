<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets\Settings;


use CpPress\Application\WP\Admin\SettingsField\Field\AccordionElementField;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class CpWidgetHeadlineSettings extends AccordionElementField
{
    public function __construct(SettingsSectionInterface $section)
    {
        $this
            ->setId('cp-widget-headline-settings')
            ->setTitle(__('Headline Widget Settings', 'cppress'))
            ->setSection($section)
            ->setName('cp-widget-headline-settings');
    }

    public function render(array $args)
    {
        $this
            ->renderAccordionTitle()
            ->renderAccordionElementOpen()
            ->renderAccordionElementClose();
    }

}