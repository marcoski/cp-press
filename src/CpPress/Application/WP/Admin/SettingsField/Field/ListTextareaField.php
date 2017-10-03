<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

class ListTextareaField extends TextareaField{
	
	private $lineSeparator;
	private $innerSeparator;
	
	public function __construct($lineSeparator = '/\r\n|\n|\r/', $innerSeparator = ':'){
		$this->lineSeparator = $lineSeparator;
		$this->innerSeparator = $innerSeparator;
	}
	
	public function setLineSeparator($lineSeparator){
		$this->lineSeparator = $lineSeparator;
		return $this;
	}
	
	public function setInnerSeparator($innerSeparator){
		$this->innerSeparator = $innerSeparator;
		return $this;
	}
	
	public function sanitize($inputs){
		return array_map(function($attr){ 
			return explode($this->innerSeparator, $attr); 
		}, array_filter(preg_split($this->lineSeparator, $inputs)));
	}
	
	protected function getFieldValue(){
		$value = parent::getFieldValue();
		
		if($value !== null && is_array($value)){
			return join("\n", array_map(function($attr){
				return join($this->innerSeparator, $attr);
			}, $value));
		}
		
		return null;
	}
	
}