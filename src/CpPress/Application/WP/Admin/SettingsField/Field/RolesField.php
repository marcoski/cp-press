<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

class RolesField extends BaseField{
	
	public function render(array $args){
		$name = $this->section->getPage().'['.$this->getName().']';
		echo '<select id="'.$this->getId().'" name="'.$name.'">';
		echo '<option value=""></option>';
		wp_dropdown_roles($this->getFieldValue());
		echo '</select>';
	}
	
	
	public function sanitize($inputs){
		return $inputs;
	}
	
	public function getType(){
		return null;
	}
}