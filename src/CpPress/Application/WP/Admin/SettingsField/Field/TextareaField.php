<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

class TextareaField extends BaseField{
	
	public function render(array $args){
		$name = $this->section->getPage().'['.$this->getName().']';
		echo '<textarea id="'.$this->getId().'" name="'.$name.'">'.$this->getFieldValue().'</textarea>';
		if(isset($args['description'])){
			echo '<p class="description" id="'.$this->getName().'-description">'.$args['description'].'</p>';
		}
	}
	
	
	public function sanitize($inputs){
		return $inputs;
	}
	
	public function getType(){
		return 'textarea';
	}
}