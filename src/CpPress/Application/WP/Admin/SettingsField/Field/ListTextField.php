<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

class ListTextField extends TextField{
	
	private $separator;
	
	public function __construct($separator = ','){
		$this->separator = $separator;
	}
	
	public function setSeparator($separator){
		$this->separator = $separator;
		return $this;
	}
	
	public function sanitize($inputs){
		return explode($this->separator, $inputs);
	}
	
	protected function getFieldValue(){
		$value = parent::getFieldValue();
		return null !== $value ? implode($this->separator, $value) : null;
	}
	
}