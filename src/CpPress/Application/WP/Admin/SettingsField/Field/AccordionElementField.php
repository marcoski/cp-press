<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\WP\Admin\SettingsField\Field;


abstract class AccordionElementField extends BaseField
{

    public function renderAccordionTitle()
    {
        echo '<h3 
        class="ui-accordion-header ui-state-default ui-corner-all" 
        role="tab" id="'.$this->getId().'-title" 
        aria-controls="'.$this->getId().'-content" 
        aria-selected="false" aria-expanded="false" 
        tabindex="-1">
            <a href="#">'.$this->getTitle().'</a>
        </h3>';

        return $this;
    }

    public function renderAccordionElementOpen()
    {
        echo '<div 
            class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" 
            id="'.$this->getId().'-content" 
            aria-labelledby="'.$this->getId().'-title" 
            role="tabpanel" aria-hidden="true">';

        return $this;
    }

    public function renderAccordionElementClose()
    {
        echo '</div>';
        return $this;
    }

    protected function renderFieldsTableOpen()
    {
        echo '<table class="form-table"><tbody>';
        return $this;
    }

    protected function renderFieldsTableClose()
    {
        echo '</tbody></table>';
        return $this;
    }

    protected function renderRowOpen(){
        echo '<tr>';
        return $this;
    }

    protected function renderRowClose(){
        echo '</tr>';
        return $this;
    }

    protected function renderFieldTitle(BaseField $field)
    {
        echo '<th scope="row">'.$field->getTitle().'</th>';
        return $this;
    }

    protected function renderFieldCell(BaseField $field, array $args)
    {
        echo '<td>';
        $field->render($args);
        echo '</td>';
        return $this;
    }

    public function sanitize($inputs)
    {
        return $inputs;
    }

    public function getType()
    {
        return 'accordion-element';
    }
}