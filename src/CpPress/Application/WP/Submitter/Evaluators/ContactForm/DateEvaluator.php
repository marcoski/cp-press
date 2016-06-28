<?php
namespace CpPress\Application\WP\Submitter\Evaluators\ContactForm;

use CpPress\Application\WP\Shortcode\ContactFormShortcode;

class DateEvaluator extends ContactFormEvaluator{
	
	
	public function evaluate($eval){
		$name = $tag->name;
		
		$value = !is_null($this->request->getParam($name))
			? trim(strtr((string) $this->request->getParam($name), "\n", " ")) : '';
		
		$min = $eval->getOption('min');
		$max = $eval->getOption('max');
		
		if($eval->isRequired() && $value == ''){
			$this->validator->invalidate($eval,  __('Please fill in the required field.', 'cppress'));
		}else if($value != '' && !$this->isDate($value)){
			$this->validator->invalidate($eval,  __('Date format that the sender entered is invalid.', 'cppress'));
		}else if($value != '' && empty($min) && $value < $min){
			$this->validator->invalidate($eval,  __('Date is earlier than minimum limit.', 'cppress'));
		}else if($value != '' && empty($max) && $max < $value){
			$this->validator->invalidate($eval,  __('Date is later than maximum limit.', 'cppress'));
		}
		
	}
	
	private function isDate($value){
		$result = preg_match( '/^([0-9]{4,})-([0-9]{2})-([0-9]{2})$/', $value, $matches);
		
		if($result){
			$result = checkdate($matches[2], $matches[3], $matches[1]);
		}
		
		return $result;
	}
	
}