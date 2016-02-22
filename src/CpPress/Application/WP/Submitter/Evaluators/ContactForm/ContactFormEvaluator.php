<?php
namespace CpPress\Application\WP\Submitter\Evaluators\ContactForm;

use CpPress\Application\WP\Submitter\Evaluators\Evaluator;
use CpPress\Application\FrontEnd\FrontContactFormController;
use Commonhelp\App\Http\Request;
use CpPress\Application\WP\Shortcode\ContactFormShortcode;
use CpPress\Application\WP\Submitter\Validators\ContactFormValidator;

abstract class ContactFormEvaluator implements Evaluator{
	
	protected $validator;
	protected $request;
	
	protected $value;
	
	public function __construct(ContactFormValidator $validator){
		$this->validator = $validator;
		$this->request = $this->validator->getRequest();
	}
	
	public function evaluate($eval){
		$name = $eval->name;
		
		$this->value = !is_null($this->request->getParam($name)) ?
		trim(wp_unslash(strtr( (string) $this->request->getParam($name), "\n", " "))) : '';
		if($eval->isRequired() && $this->value == ''){
			$this->validator->invalidate($eval, __('Please fill in the required field.', 'cppress'));
		}
	}
	
	protected function checkLength(ContactFormShortcode $eval){
		if($this->value != ''){
			$maxLength = $eval->getMaxLengthOption();
			$minLength = $eval->getMinLengthOption();
				
			if($maxLength && $minLength && $maxLength < $minLength){
				$maxLength = $minLength = null;
			}
				
			$codeUnits = $this->countCodeUnit($this->value);
			if($codeUnits !== false){
				if($maxLength && $maxLength < $codeUnits){
					$this->validator->invalidate($eval, __('There is a field that the user input is longer than the maximum allowed length', 'cppress'));
				}else if($minLength && $codeUnits < $minLength){
					$this->validator->invalidate($eval, __('There is a field that the user input is shorter than the minimum allowed length', 'cppress'));
				}
			}
		}
	}
	
	protected function countCodeUnit($string){
		if(!function_exists('mb_convert_encoding')){
			return false;
		}
		
		$encoding = mb_detect_encoding($string, mb_detect_order(), true);
		if($encoding){
			$string = mb_convert_encoding($stirng, 'UTF-16', $encoding);
		}else{
			$string = mb_convert_encoding($string, 'UTF-16', 'UTF-8');
		}
		
		$bCount = mb_strlen($string, '8bit');
		
		return floor($bCount / 2);
	}
	
}