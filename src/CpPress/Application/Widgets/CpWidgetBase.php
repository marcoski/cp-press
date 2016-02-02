<?php
namespace CpPress\Application\Widgets;

use WP_Widget;
use Commonhelp\WP\WPIController;
use Commonhelp\Util\Inflector;
use Commonhelp\WP\WPTemplate;
use Commonhelp\WP\WPContainer;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\CpPress;
use CpPress\Application\WP\Hook\Filter;

abstract class CpWidgetBase extends WP_Widget implements WPIController{

	private $vars;
	protected $templateDirs;
	protected $icon;
	protected $container;
	protected $adminScripts=array();
	private $template;
	protected $uri;
	protected $scriptsPath;
	protected $action;
	private $scripts;
	protected $filter;

	public function __construct($name, $widget_options = array(), $control_options = array(), array $templateDirs=array()){
		if(!empty($templateDirs)){
			$this->templateDirs = $templateDirs;
		}else{
			$this->templateDirs = array(dirname(dirname(dirname(CpPress::$FILE))));
		}
		$this->action = '';
		$this->vars = array();
		$id_base = Inflector::underscore(
			(new \ReflectionClass($this))->getShortName()
		);
		$widget_options['classname'] = Inflector::dasherize($id_base);
		parent::__construct(
			$id_base,
			$name,
			$widget_options,
			$control_options
		);
		$this->_register();
		$this->template = new WPTemplate($this);
	}

	public static function getWidgets(){
		$coreWidgets = glob(dirname(plugin_dir_path(__FILE__)).'/Widgets/*.php');
		$widgets = array();
		foreach($coreWidgets as $widget){
			$info = pathinfo($widget);
			if($info['filename'] != 'CpWidgetBase'){
				$widgets[] = 'CpPress\\Application\\Widgets\\'.$info['filename'];
			}
		}

		return $widgets;
	}
	
	public function setContainer(WPContainer $c){
		$this->container = $c;
	}
	
	public function setFilter(Filter $filter){
		$this->filter = $filter;
	}
	
	public function setUri($uri){
		$this->uri = $uri.'/templates/widget/'.$this->id_base;
		$this->scriptsPath = $this->templateDirs[0].'/templates/widget/'.$this->id_base;
	}
	
	public function setScriptsObj(Scripts $scripts){
		$this->scripts = $scripts;
	}
	
	public function enqueueAdminScripts(){
		$oldUris = $this->scripts->getUris();
		$this->scripts->setUri(
			array($this->scriptsPath, $this->uri),
			array($this->scriptsPath, $this->uri)
		);
		foreach($this->adminScripts as $s){
			$this->scripts->enqueue($s['source'], $s['deps']);
		}
		$this->setUri($oldUris['base'], $oldUris['child']);
	}

	public function assign($name, $value){
		$this->vars[$name] = $value;
		return true;
	}

	public function getTemplateDirs(){
		return $this->templateDirs;
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$this->assign('args', $args);
		$this->assign('instance', $instance);
		$this->assign('filter', $this->filter);
		return $this->render();
	}

	 /**
 	 * Outputs the options form on admin
 	 *
 	 * @param array $instance The widget options
 	 */
 	public function form($instance) {
		$this->assign('widget', $this);
		$this->assign('instance', $instance);
		$this->assign('id_base', $this->id_base);
 		return $this->render();
 	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update($new_instance, $old_instance) {
		return $new_instance;
	}

	public function getAction(){
		if(is_admin()){
			$action = 'backend';
		}else{
			$action = 'frontend';
		}
		return $this->id_base.'/'.$action;
	}

	public function getIcon(){
		return $this->icon;
	}

	protected function render(){
		$this->template->setVars($this->vars);
		return $this->template->render();
	}

	public function getAppName(){
		return 'WidgetApp';
	}
	
	protected function formatStyles($styles){
		$style = '';
		foreach($styles as $key => $value){
			if(!is_null($value)){
				$style .= $key . ':' . $value . '; ';
			}
		}
		return rtrim($style);
	}

}
