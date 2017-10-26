<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets\Settings;


use CpPress\Application\WP\Admin\SettingsField\Field\AccordionElementField;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class CpWidgetRssSettings extends AccordionElementField
{
    public function __construct(SettingsSectionInterface $section)
    {
        $this
            ->setId('cp-widget-rss-settings')
            ->setTitle(__('Rss Widget Settings', 'cppress'))
            ->setSection($section)
            ->setName('cp-widget-rss-settings');
    }

    public function render(array $args)
    {
        $this
            ->renderAccordionTitle()
            ->renderAccordionElementOpen()
            ->renderAccordionElementClose();
    }
}