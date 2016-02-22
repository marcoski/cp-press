<?php
namespace CpPress\Application\WP\Submitter\Evaluators\ContactForm;

use CpPress\Application\WP\Shortcode\ContactFormShortcode;

class TextareaEvaluator extends ContactFormEvaluator{
	
	
	public function evaluate($eval){
		$value = parent::evaluate($eval);
		$type = $eval->type;
		
		$this->checkLength($eval);
		
	}
	
}