<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets\Settings;


use CpPress\Application\WP\Admin\SettingsField\Field\AccordionElementField;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class CpWidgetSliderSettings extends AccordionElementField
{
    public function __construct(SettingsSectionInterface $section)
    {
        $this
            ->setId('cp-widget-slider-settings')
            ->setTitle(__('Slider Widget Settings', 'cppress'))
            ->setSection($section)
            ->setName('cp-widget-slider-settings');
    }

    public function render(array $args)
    {
        $this
            ->renderAccordionTitle()
            ->renderAccordionElementOpen()
            ->renderAccordionElementClose();
    }
}