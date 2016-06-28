<?php
namespace CpPress\Application\WP\Submitter;

use Commonhelp\App\Http\Request;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;
use Commonhelp\Util\Inflector;
class MailPoetSubmitter extends Submitter{
	
	public function __construct(Request $request, Filter $filter, Hook $hook){
		parent::__construct($request, $filter, $hook);
	}
	
	protected function submit(){
		$type = Inflector::camelize($this->request->getParam('_cppress-mailpoet_type'));
		$id = $this->request->getParam('_cppress-mailpoet-id');
		
		$submit = 'submit' . $type;
		
		return $this->$submit($id);
	}
	
	protected function submitDefault($id){
		$email = $this->request->getParam('cppress-mailpoet-email');
		$result = array();
		if(is_null($id) || !is_numeric($id)){
			return array('valid' => false, 'message' => __('Invalid or empty form', 'cppress'));
		}
		if(is_null($email) || !sanitize_email($email)){
			return array('valid' => false, 'message' => __('Invalid or empty email address', 'cppress'));
		}
		
		$dataSubscriber = array(
			'user' => array('email' => $email),
			'user_list' => array('list_ids' => array($id))
		);
		
		$helperUser = \WYSIJA::get('user', 'helper');
		$helperUser->addSubscriber($dataSubscriber);
		
		return array('valid' => true);
	}
	
	protected function submitTemplate($id){
		$values = $this->request->getParam('cppress-mailpoet');
		$result = array();
		if(is_null($id) || !is_numeric($id)){
			return array('valid' => false, 'message' => __('Invalid or empty form', 'cppress'));
		}
		
		$data = array();
		foreach($values as $name => $val){
			if(strpos($name, '*')){
				$n = trim($name, '*');
				if($n == 'email' && ($n == '' || !sanitize_email($val))){
					return array('valid' => false, 'message' => __('Invalid or empty email address', 'cppress'));
				}
				if(($n == 'firstname' || $n == 'lastnam') && $n == ''){
					return array('valid' => false, 'message' => __('Invalid or empty field', 'cppress'));
				}
			}
			$name = trim($name, '*');
			$data[$name] = $val;
		}
		$dataSubscriber = array(
				'user' => $data,
				'user_list' => array('list_ids' => array($id))
		);
		
		$helperUser = \WYSIJA::get('user', 'helper');
		$helperUser->addSubscriber($dataSubscriber);
		
		return array('valid' => true);
	}
	
	public function ajaxSubmit($instance, $args){
		return $this->submit();
	}
	
	public function nonajaxSubmit($instance, $args){
		return $this->submit();
	}
	
}