<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

class EnabledField extends BaseField{
	
	public function render(array $args){
		$attrs = isset($args['attrs']) ? $this->renderAttrs($args['attrs']) : '';
		$attrs .= ' '.checked($this->getFieldValue(), 1, false);
		echo sprintf(
			$this->tags['checkbox'],
			$this->getId(),
			$this->section->getPage(),
			$this->getName(),
			1,
			$attrs
		);
		if(isset($args['description'])){
			echo '<p class="description" id="'.$this->getName().'-description">'.$args['description'].'</p>';
		}
	}
	
	
	public function sanitize($inputs){
		return $inputs;	
	}
	
	public function getType(){
		return null;
	}
}