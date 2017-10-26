<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets\Settings;


use CpPress\Application\WP\Admin\SettingsField\Field\AccordionElementField;
use CpPress\Application\WP\Admin\SettingsField\Field\EnabledField;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class CpWidgetContentSettings extends AccordionElementField
{
    /** @var EnabledField */
    private $disableIcon;

    /** @var  EnabledField */
    private $disableAdvancedOptions;

    /** @var  EnabledField */
    private $disableViewOptions;

    public function __construct(SettingsSectionInterface $section)
    {
        $this->section = $section;
        $this
            ->setId('cp-widget-content-settings')
            ->setTitle(__('Content Widget Settings', 'cppress'))
            ->setSection($section)
            ->setName('cp-widget-content-settings');

        $this->setDisableIcon();
        $this->setDisableAdvancedOptions();
        $this->setDisableViewOptions();
    }

    public function render(array $args)
    {
        $this
            ->renderAccordionTitle()
            ->renderAccordionElementOpen()
                ->renderFieldsTableOpen()
                    ->renderRowOpen()
                        ->renderFieldTitle($this->disableIcon, $args)
                        ->renderFieldCell($this->disableIcon, $args)
                    ->renderRowClose()
                    ->renderRowOpen()
                        ->renderFieldTitle($this->disableAdvancedOptions, $args)
                        ->renderFieldCell($this->disableAdvancedOptions, $args)
                    ->renderRowClose()
                    ->renderRowOpen()
                        ->renderFieldTitle($this->disableViewOptions, $args)
                        ->renderFieldCell($this->disableViewOptions, $args)
                    ->renderRowClose()
                ->renderFieldsTableClose()
            ->renderAccordionElementClose();

    }

    private function setDisableIcon(){
        $enabledField = new EnabledField();
        $enabledField
            ->setId('cp-widget-content-settings-disable-icon')
            ->setTitle(__('Disable Icon Section', 'cppress'))
            ->setSection($this->section)
            ->setName('cp-widget-content-settings-disable-icon');

        $this->disableIcon = $enabledField;
        $this->disableIcon->addSilentField();
    }

    private function setDisableAdvancedOptions(){
        $enabledField = new EnabledField();
        $enabledField
            ->setId('cp-widget-content-settings-disable-advanced-options')
            ->setTitle(__('Disable Advanced Options', 'cppress'))
            ->setSection($this->section)
            ->setName('cp-widget-content-settings-disable-advanced-options');

        $this->disableAdvancedOptions = $enabledField;
        $this->disableAdvancedOptions->addSilentField();
    }

    private function setDisableViewOptions(){
        $enabledField = new EnabledField();
        $enabledField
            ->setId('cp-widget-content-settings-disable-view-options')
            ->setTitle(__('Disable View Opions', 'cppress'))
            ->setSection($this->section)
            ->setName('cp-widget-content-settings-disable-view-options');

        $this->disableViewOptions = $enabledField;
        $this->disableViewOptions->addSilentField();
    }
}