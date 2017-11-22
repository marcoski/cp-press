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
		$list = intval($this->request->getParam('_cppress_mailpoet-list', 1));
		$submit = 'submit' . $type;

		try{
            return $this->$submit($id, $list);
        }catch (\Exception $e){
		    pr($e->getTraceAsString());
		    return;
        }
	}

	protected function submitDefault($id, $list){
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
			'user_list' => array('list_ids' => array($list))
		);

		$helperUser = \WYSIJA::get('user', 'helper');
		$helperUser->addSubscriber($dataSubscriber);

		return array('valid' => true);
	}

	protected function submitTemplate($id, $list){
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
			'user_list' => array('list_ids' => array($list))
		);

        if(!class_exists('WYSIJA')){
            throw new \Exception('No class WYSIJA found');
        }
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