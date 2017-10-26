<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\Widgets;


abstract class CpWidgetBaseCustom extends CpWidgetBase
{

    public function __construct($name, array $widget_options = array(), array $control_options = array(), array $templateDirs = array())
    {
        $templateDirs =  array( get_template_directory() . '/', get_stylesheet_directory() . '/' );
        parent::__construct($name, $widget_options, $control_options, $templateDirs);
    }

    protected function render()
    {
        $template = 'template-parts/widgets/'.$this->getAction();
        return $this->template->inc($template, $this->vars);
    }
}