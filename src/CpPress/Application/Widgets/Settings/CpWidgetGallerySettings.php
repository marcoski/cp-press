<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets\Settings;


use CpPress\Application\WP\Admin\SettingsField\Field\AccordionElementField;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class CpWidgetGallerySettings extends AccordionElementField
{
    public function __construct(SettingsSectionInterface $section)
    {
        $this
            ->setId('cp-widget-gallery-settings')
            ->setTitle(__('Gallery Widget Settings', 'cppress'))
            ->setSection($section)
            ->setName('cp-widget-gallery-settings');
    }

    public function render(array $args)
    {
        $this
            ->renderAccordionTitle()
            ->renderAccordionElementOpen()
            ->renderAccordionElementClose();
    }
}