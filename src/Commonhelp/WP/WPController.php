<?php
namespace Commonhelp\WP;

use Commonhelp\App\AbstractController;
use Commonhelp\App\Http\RequestInterface;
use Commonhelp\App\Http\DataResponse;
use Commonhelp\App\Http\TemplateResponse;
use Commonhelp\App\Http\JsonResponse;
use Commonhelp\App\Exception\ControllerException;
use Commonhelp\Util\Inflector;
use Commonhelp\App\Http\Commonhelp\App\Http;

abstract class WPController extends AbstractController implements WPIController{
	
	protected $action;
	protected $wpAjaxData;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array()){
		parent::__construct($appName, $request);
		$this->action = null;
		$this->wpAjaxData = array();
		$this->templateDirs = $templateDirs;
		$this->registerResponder('template', function($response){
			try{
				return new TemplateResponse(new WPTemplate($this), $this->vars);
			}catch(TemplateNotFoundException $ex){
				return WPTemplate::renderException($this, $ex);
			}
		});
		$this->registerResponder('wpjson', function($response){
			if(preg_match("/^(xhr|ajax)(_){0,1}(?P<action>\w+)/", $this->action, $matches)){
				$this->action = lcfirst($matches['action']);
			}
			$template = $this->render($response, 'template');
			$jsonData = array(
				'what'		=> $this->getAppName(),
				'action'	=> $this->getAction(),
				'data'		=> $template->render()
			);
			if(!empty($this->wpAjaxData)){
				$jsonData['extra'] = $this->wpAjaxData;
			}
			return new JsonResponse($jsonData);
		});
		$this->registerResponder('string', function($response){
			return new DataResponse($response);
		});
	}
	
	public function setTemplateDirs($templateDirs){
		$this->templateDirs = $templateDirs;
	}
	
	public function setAction($action){
		$this->action = $action;
	}
	
	public function getAction(){
		return $this->action;
	}
	
	public function setWpAjaxData($value, $data){
		$this->wpAjaxData[$value] = $data;
	}
	
	public function render($response, $format = 'json'){
		if(array_key_exists($format, $this->responders)) {
			$responder = $this->responders[$format];
			return $responder($response);
		} else {
			throw new RenderException('No responder registered for format ' .
					$format . '!');
		}
	}
	
}