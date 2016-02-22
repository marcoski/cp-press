<?php
namespace CpPress\Application\WP\Submitter\Evaluators\ContactForm;

use CpPress\Application\WP\Shortcode\ContactFormShortcode;

class NumberEvaluator extends ContactFormEvaluator{
	
	
	public function evaluate($eval){
		$name = $tag->name;
		
		$value = !is_null($this->request->getParam($name))
			? trim(strtr((string) $this->request->getParam($name), "\n", " ")) : '';
		
		$min = $eval->getOption('min', 'signed_int', true);
		$max = $eval->getOption('max', 'signed_int', true);
		
		if($eval->isRequired() && $value == ''){
			$this->validator->invalidate($eval,  __('Please fill in the required field.', 'cppress'));
		}else if($value != '' && !$this->isNumber($value)){
			$this->validator->invalidate($eval,  __('Number format that the sender entered is invalid.', 'cppress'));
		}else if($value != '' && $min != '' && (float) $value < (float) $min){
			$this->validator->invalidate($eval,  __('Number is smaller than minimum limit.', 'cppress'));
		}else if($value != '' && $max != '' && (float) $max < (float) $value){
			$this->validator->invalidate($eval,  __('Number is larger than maximum limit.', 'cppress'));
		}
		
	}
	
	private function isNumber($value){
		return is_numeric($value);
	}
	
}