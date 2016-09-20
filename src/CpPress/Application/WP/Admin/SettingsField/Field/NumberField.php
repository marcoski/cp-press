<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

class NumberField extends TextField{
	
	private $min;
	private $max;
	
	public function __construct($min = null, $max = null){
		$this->min = null === $min ?: $min;
		$this->max = null === $max ?: $max;
	}
	
	protected function renderAttrs($attrs){
		$attrs = parent::renderAttrs($attrs);
		
		if(null !== $this->min){
			$attrs .= ' min="'.$this->min.'"';
		}
		
		if(null !== $this->max){
			$attrs .= ' max="'.$this->max.'"';
		}
		
		return $attrs;
	}
	
	public function setMin($min){
		$this->min = $min;
		return $this;
	}
	
	public function setMax($max){
		$this->max = $max;
		return $this;
	}
	
	public function getType(){
		return 'number';
	}
	
}