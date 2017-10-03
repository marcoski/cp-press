<?php
namespace CpPress\Application\WP\Submitter\Evaluators\ContactForm;

use CpPress\Application\WP\Shortcode\ContactFormShortcode;

class AcceptanceEvaluator extends ContactFormEvaluator{
	
	
	public function evaluate($eval){
		$name = $eval->name;
		$value = (!is_null($this->request->getParam($name)) ? 1 : 0);
		
		$invert = $eval->hasOption('invert');
		if($invert && $value || !$invert && !$value){
			$this->validator->invalidate($eval, __('You must accept user terms', 'cppress'));
		}
		
	}
	
}