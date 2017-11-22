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
		if(!isset($atts['submit'])){
			$atts['submit'] = 'default';
		}
		if(!isset($atts['list'])){
			$atts['list'] = 1;
		}
		$formModel = \WYSIJA::get('forms', 'model');
		$form = $formModel->getOne(array('form_id' => $atts['id']));
		if(!empty($form)){
			$this->hiddenFields($atts['id'], $atts['type'], $atts['submit'], $atts['list']);
			$this->assign('mailpoetConfig', \WYSIJA::get('config', 'model'));
			$this->assign('id', $atts['id']);
			$this->assign('formResult', FrontEndApplication::getFormResult('cppress-mailpoet'));
			if($atts['type'] == 'default'){
				$template = 'default';
			}else if($atts['type'] == 'template'){
				if(!isset($atts['template'])){
					$template = 'template-parts/mailpoet-form';
				}else{
					$template = 'template-parts/' . $atts['template'];
				}
				$this->setTemplateDirs(array(get_template_directory().'/', get_stylesheet_directory().'/'));
			}

			return new WPTemplateResponse($this, $template);
		}

		return '';
	}

	private function hiddenFields($id, $type, $submit, $list=null){
		$hiddenFields = array(
			'_cppress-mailpoet' => 1,
			'_cppress-mailpoet-version' => 2,
			'_wpnonce' => wp_create_nonce('cppress-mailpoet'),
			'_cppress-mailpoet-id' => $id,
			'_cppress-mailpoet_type' => $type
		);
		if(is_int($list)){
			$hiddenFields['_cppress_mailpoet-list'] = $list;
		}else{
			$model = \WYSIJA::get('list', 'model');
			foreach($model->getLists() as $modelList){
				if($modelList['name'] === $list){
					$list = $modelList['list_id'];
					break;
				}
			}
			$hiddenFields['_cppress_mailpoet-list'] = $list;
		}
		if('ajax' === $submit){
			$hiddenFields['_cppress_front_ajax'] = 1;
			$hiddenFields['_cppress-mailpoet-isajaxcall'] = 1;
		}
		$this->assign('hiddenFields', $hiddenFields);
	}

	private function getTemplate($instance, $tPreName){
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