<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use Commonhelp\WP\WPTemplate;
use Commonhelp\Util\Inflector;
use Commonhelp\App\Http\RequestInterface;
use Commonhelp\WP\WPTemplateResponse;
use CpPress\Application\WP\Submitter\Submitter;
use CpPress\Application\FrontEndApplication;

class FrontMailPoetController extends WPController{
	
	private $filter;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
	}
	
	public function submit(Submitter $submitter){
		if($submitter->checkNonce('cppress-mailpoet')){
			$error = new \WP_Error('invalid', __('Invalid form send', 'cppress'));
			echo $error->get_error_message();
			return;
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !is_null($this->getParam('_cppress-mailpoet_type', null))){
			if(!is_null($this->getParam('_cppress-mailpoet-isajaxcall'))){
				$result = $submitter->ajaxSubmit(null, null);
				return $this->filter->apply('cppress-mailpoet-ajax-json', $result);
			}
			
			$result = $submitter->nonajaxSubmit(null, null);
			return $this->filter->apply('cppress-mailpost-submit', $result);
		}
		
	}
	
	public function doShortcode($atts){
		if(!isset($atts['type'])){
			$atts['type'] = 'default';
		}
		$formModel = \WYSIJA::get('forms', 'model');
		$form = $formModel->getOne(array('form_id' => $atts['id']));
		if(!empty($form)){
			$this->hiddenFields($atts['id'], $atts['type']);
			$this->assign('mailpoetConfig', \WYSIJA::get('config', 'model'));
			$this->assign('id', $atts['id']);
			$this->assign('formResult', FrontEndApplication::getFormResult('cppress-mailpoet'));
			return new WPTemplateResponse($this, $atts['type']);
		}
		
		return '';
	}
	
	private function hiddenFields($id, $type){
		$hiddenFields = array(
				'_cppress-mailpoet' => 1,
				'_wpnonce' => wp_create_nonce('cppress-mailpoet'),
				'_cppress-mailpoet-id' => $id,
				'_cppress-mailpoet_type' => $type
		);
		$this->assign('hiddenFields', $hiddenFields);
	}
	
	private function assignTemplate($instance, $tPreName){
		$template = new WPTemplate($this);
		$template->setTemplateDirs(array(get_template_directory().'/', get_stylesheet_directory().'/'));
		if($instance['wtitle'] !== ''){
			$templateName = $this->filter->apply('cppress_widget_post_template_name',
					'template-parts/' . $tPreName . '-' .
					Inflector::delimit(Inflector::camelize($instance['wtitle']), '-'), $instance);
		}else{
			$templateName = $this->filter->apply('cppress_widget_post_template_name',
					'template-parts/' . $tPreName, $instance);
		}
		$this->assign('templateName', $templateName);
		$this->assign('template', $template);
	}
	
	
	
}