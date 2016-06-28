<?php
namespace CpPress\Application\WP\Submitter\Evaluators\ContactForm;

use CpPress\Application\WP\Shortcode\ContactFormShortcode;

class SelectEvaluator extends ContactFormEvaluator{
	
	
	public function evaluate($eval){
		$name = $eval->name;
		$values = $this->request->getParam($name);
		if(!is_null($values) && is_array($values)){
			foreach($values as $k => $v){
				if($v === ''){
					unset($values[$name][$k]);
				}
			}
		}
		
		$empty = !isset($values[$name]) || empty($values[$name]) && $values[$name] !== 0;
		
		if($eval->isRequired() && $empty){
			$this->validator->invalidate($eval,  __('Please fill in the required field.', 'cppress'));
		}
		
	}
	
}