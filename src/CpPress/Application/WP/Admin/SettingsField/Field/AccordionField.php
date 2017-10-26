<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\WP\Admin\SettingsField\Field;


abstract class AccordionField extends BaseField
{

    /**
     * @var BaseField[]
     */
    protected $fields = [];

    public function render(array $args){
        echo '<div class="accordion-settings ui-accordion ui-widget ui-helper-reset" role="tablist">';
        /** @var BaseField $field */
        foreach($this->fields as $field){
            $field->render($args);
        }

        echo '</div>';
        if(isset($args['description'])){
            echo '<p class="description" id="'.$this->getName().'-description">'.$args['description'].'</p>';
        }
    }

    public function addAccordionField(BaseField $field){
        $this->fields[$field->getId()] = $field;
    }

    public function getAccordionField($field){
        $id = $field;
        if($field instanceof BaseField){
            $id = $field->getId();
        }

        if($this->hasAccordionField($field)){
            return $this->fields[$id];
        }

        return null;
    }

    public function getAllAccordionFields(){
        return $this->fields;
    }

    public function hasAccordionField($field){
        $id = $field;
        if($field instanceof BaseField){
            $id = $field->getId();
        }

        return isset($this->fields[$id]);
    }

    public function sanitize($inputs){
        return $inputs;
    }

    public function getType(){
        return 'accordion';
    }

}