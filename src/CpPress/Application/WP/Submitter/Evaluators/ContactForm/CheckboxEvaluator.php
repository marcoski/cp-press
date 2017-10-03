<?php
namespace CpPress\Application\WP\Submitter\Evaluators\ContactForm;

use CpPress\Application\WP\Shortcode\ContactFormShortcode;

class CheckboxEvaluator extends ContactFormEvaluator{
	
	
	public function evaluate($eval){
		$name = $eval->name;
		$type = $eval->type;
		
		$value = !is_null($this->request->getParam($name)) ? (array) $this->request->getParam($name) : array();
		if($eval->isRequired() && empty($value)){
			$this->validator->invalidate($eval,  __('Please fill in the required field.', 'cppress'));
		}
		
	}
	
}