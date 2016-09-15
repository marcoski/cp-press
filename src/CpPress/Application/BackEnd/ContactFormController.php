<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Shortcode\ContactFormShortcodeManager;
use Commonhelp\WP\WPTemplate;
use CpPress\Application\WP\Hook\Filter;

class ContactFormController extends WPController{
	
	private $options;
	private $fields;
	
	private $backEndFilter;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $backEndFilter){
		parent::__construct($appName, $request, $templateDirs);
		$this->backEndFilter = $backEndFilter;
		$this->options = get_option('cppress-options-contactform');
		$this->fields = ContactFormShortcodeManager::$fields;
	}
	
	public function form($instance, $widget){
		$this->assign('fields', $this->fields);
		$this->assign('fields_json', htmlspecialchars(json_encode($this->fields, JSON_HEX_TAG)));
		$this->assign('instance', $instance);
		$this->assign('widget', $widget);
	}
	
	public function dialog_form(){
		
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_taggenerator(){
		$args = json_decode(stripslashes($this->getParam('args')), true);
		$name = $args['hclass'] . '-' . mt_rand(100, 999);
		$this->assign('name', $name);
		$this->assign('args', $args);
		$this->assign('shortcode', '[' . $args['hclass'] .' ' . $name . ']');
	}
	
	public function mail_template_form($instance, $widget, $link, WPTemplate $template){
		$this->assign('instance', $instance);
		$this->assign('widget', $widget);
		$this->assign('link', $link);
		$this->assign('template', $template);
		$this->assign('filter', $this->backEndFilter);
	}
	
}