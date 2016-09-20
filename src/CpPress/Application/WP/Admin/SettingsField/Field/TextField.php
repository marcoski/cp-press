<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

class TextField extends BaseField{
	
	public function render(array $args){
		echo sprintf(
			$this->tags['input'], 
			$this->getType(),
			$this->getId(),
			$this->section->getPage(),
			$this->getName(),
			$this->getFieldValue() !== null ? $this->getFieldValue() : '',
			isset($args['attrs']) ? $this->renderAttrs($args['attrs']) : ''
		);
		if(isset($args['description'])){
			echo '<p class="description" id="'.$this->getName().'-description">'.$args['description'].'</p>';
		}
	}
	
	
	public function sanitize($inputs){
		return $inputs;
	}
	
	public function getType(){
		return 'text';
	}
}