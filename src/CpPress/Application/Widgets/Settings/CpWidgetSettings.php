<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets\Settings;


use CpPress\Application\WP\Admin\SettingsField\Field\AccordionField;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class CpWidgetSettings extends AccordionField
{

    public function __construct(SettingsSectionInterface $section)
    {
        $this->setSection($section);
        $this->addAccordionField(new CpWidgetButtonSettings($section));
        $this->addAccordionField(new CpWidgetContactFormSettings($section));
        $this->addAccordionField(new CpWidgetContentSettings($section));
        $this->addAccordionField(new CpWidgetFacebookSettings($section));
        $this->addAccordionField(new CpWidgetGallerySettings($section));
        $this->addAccordionField(new CpWidgetGoogleMapSettings($section));
        $this->addAccordionField(new CpWidgetHeadlineSettings($section));
        $this->addAccordionField(new CpWidgetLoopSettings($section));
        $this->addAccordionField(new CpWidgetMediaSettings($section));
        $this->addAccordionField(new CpWidgetMenuSettings($section));
        $this->addAccordionField(new CpWidgetPortfolioSettings($section));
        $this->addAccordionField(new CpWidgetRssSettings($section));
        $this->addAccordionField(new CpWidgetSliderSettings($section));
        $this->addAccordionField(new CpWidgetSocialbuttonSettings($section));
        $this->addAccordionField(new CpWidgetTextSettings($section));
        $this->addAccordionField(new CpWidgetTwitterSettings($section));
    }

    public function render(array $args)
    {
        parent::render($args);
    }

    public function sanitize($inputs)
    {
        return parent::sanitize($inputs);
    }

    public static function getOptions($widgetSettingsBaseName)
    {
        if(($options = get_option('cppress-options-general')) === false){
            $options = [];
        }
        $toReturn = [];
        foreach($options as $name => $value){
            if(preg_match('/'.$widgetSettingsBaseName.'/', $name)){
                $toReturn[$name] = $value;
            }
        }

        return $toReturn;
    }
}