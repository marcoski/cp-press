<?php
namespace CpPress\Application\WP\Submitter\Validators;

use Commonhelp\App\Http\Request;
use CpPress\Application\WP\Shortcode\ContactFormShortcode;
use CpPress\Application\WP\Submitter\Evaluators\Evaluator;
use CpPress\Application\WP\Submitter\Evaluators\ContactForm\ContactFormEvaluator;

class ContactFormValidator extends Validator{
	
	private $uploadedFile;
	
	public function __construct(Request $request){
		parent::__construct($request);
		$this->uploadedFile = null;
	}
	
	public function getUploadedFile(){
		return $this->uploadedFile;
	}
	
	public function invalidate($context, $message){
		$tag = null;
		if($context instanceof ContactFormShortcode){
			$tag = $context;
		}else if(is_array($context)){
			$tag = new ContactFormShortcode($context, $this->request);
		}
		
		$name = !is_null($tag) ? $tag->name : null;
		
		if(is_null($name)){
			return;
		}
		
		if($this->isValid($name)){
			$id = $tag->getIdOption();
			if(!$id){
				$id = null;
			}
			
			$this->invalidFields[$name] = array(
				'reason' => (string) $message,
				'idref' => $id
			);
			
		}
	}
	
	public function validate($evaluator, $eval){
		if(!($eval instanceof ContactFormShortcode)){
			$eval = new ContactFormShortcode($eval, $this->request);
		}
		$evaluator = trim($evaluator, '*');
		if(!($evaluator instanceof ContactFormEvaluator)){
			if($evaluator == 'email' || $evaluator == 'phone'){
				$evaluator = 'text';
			}else if($evaluator == 'radio'){
				$evaluator = 'checkbox';
			}
			$evaluator = 'CpPress\\Application\\WP\Submitter\\Evaluators\\ContactForm\\'. ucfirst($evaluator) . 'Evaluator';
			$evaluator = new $evaluator($this);
		}
		
		if($evaluator == 'file'){
			$this->uploadedFile = $evaluator->evaluate($eval);
		}
		$evaluator->evaluate($eval);
		return $this;
	}
	
	
	
}