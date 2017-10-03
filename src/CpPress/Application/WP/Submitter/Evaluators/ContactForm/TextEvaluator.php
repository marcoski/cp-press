<?php
namespace CpPress\Application\WP\Submitter\Evaluators\ContactForm;

use CpPress\Application\WP\Shortcode\ContactFormShortcode;

class TextEvaluator extends ContactFormEvaluator{
	
	
	public function evaluate($eval){
		parent::evaluate($eval);
		if($eval->baseType == 'email'){
			if($this->value != '' && !filter_var($this->value, FILTER_VALIDATE_EMAIL)){
				$this->validator->invalidate($eval, __('Invalid email address', 'cppress'));
			}
		}
		if($eval->baseType == 'url'){
			if($this->value != '' && !filter_var($this->value, FILTER_VALIDATE_URL)){
				$this->validator->invalidate($eval, __('Invalid url', 'cppress'));
			}
		}
		if($eval->baseType == 'phone'){
			if($this->value != '' && !$this->isPhone($this->value)){
				$this->validator->invalidate($eval, __('Invalid phone number', 'cppress'));
			}
		}
		
		$this->checkLength($eval);
	}
	
	private function isPhone($phone){
		return preg_match('/^[+]?[0-9() -]*$/', $phone);
	}
}