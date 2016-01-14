<?php
namespace CpPress\Application\Widgets;

use WP_Widget;
use Commonhelp\WP\WPIController;
use Commonhelp\Util\Inflector;
use Commonhelp\WP\WPTemplate;
use Commonhelp\WP\WPContainer;
use CpPress\CpPress;

abstract class CpWidgetBase extends WP_Widget implements WPIController{

	private $vars;
	protected $templateDirs;
	protected $icon;
	protected $container;

	public function __construct($name, $widget_options = array(), $control_options = array(), array $templateDirs=array()){
		if(!empty($templateDirs)){
			$this->templateDirs = $templateDirs;
		}else{
			$this->templateDirs = array(dirname(dirname(dirname(CpPress::$FILE))));
		}
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
		// outputs the content of the widget
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
		$template = new WPTemplate($this);
		$template->setVars($this->vars);
		return $template->render();
	}

	public function getAppName(){
		return 'WidgetApp';
	}

}
