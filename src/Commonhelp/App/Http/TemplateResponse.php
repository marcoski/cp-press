<?php
namespace Commonhelp\App\Http;

use Commonhelp\App\Template\TemplateBase;

class TemplateResponse extends Response {
	
	protected $engine;
	
	protected $params;
	
	/**
	 * constructor of TemplateResponse
	 * @param string $appName the name of the app to load the template from
	 * @param string $templateName the name of the template
	 * @param array $params an array of parameters which should be passed to the
	 * template
	 * @param string $renderAs how the page should be rendered, defaults to user
	 * @since 6.0.0 - parameters $params and $renderAs were added in 7.0.0
	 */
	public function __construct(TemplateBase $engine, $params) {
		$this->engine = $engine;
		$this->params = $params;
	}
	
	public function setParams(array $params){
		$this->params = $params;
		return $this;
	}

	public function getParams(){
		return $this->params;
	}
	
	public function getTemplateName(){
		return $this->engine->getTemplate();
	}
	
	public function setEngine(TemplateBase $engine){
		$this->engine = $engine;
	}
	
	public function getEngine(){
		return $this->engine;
	}
	

	/**
	 * Returns the rendered html
	 * @return string the rendered html
	 */
	public function render(){
		// \OCP\Template needs an empty string instead of 'blank' for an unwrapped response
	
		$this->engine->setVars($this->params);
		return $this->engine->render();
	}
}