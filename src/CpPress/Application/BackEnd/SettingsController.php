<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\MetaType\PostType;

class SettingsController extends WPController{
	
	private $settings;
	private $themeUri;
	
	private $formTags = array(
		'input' => '<input type="text" id="%s" name="cppress-options-%s[%s]" value="%s" %s/>',
		'file' => '<input type="file" name="cppress-options-%s[%s]" id="%s" value="%s" %s/>',
		'select' => '<select id="%s" name="cppress-options-%s[%s]">%s</select>',
		'selectmultiple' => '<select id="%s" name="cppress-options-%s[%s][]" multiple>%s</select>',
		'checkbox' => '<input type="checkbox" id="%s" name="cppress-options-%s[%s][]" value="%s" %s/>',
		'radio' => '<input type="radio" name="cppress-options-%s[%s]" id="%s" value="%s" %s />',
		'optiongroupstart' => '<optgroup label="%s">',
		'optiongroupend' => '</optgroup>',
		'selectoption' => '<option value="%s" %s>%s</option>',
	);
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), $themeUri, Settings $settings){
		parent::__construct($appName, $request, $templateDirs);
		$this->settings = $settings;
		$this->themeUri = $themeUri;
		$this->assign('_settings', $this->settings);
	}
	
	public function main(){
		$this->assign('tab', $this->getParam('tab', 'general'));
	}
	
	public function general(){
		$this->assign('root', $this->themeUri.'/assets');
	}
	
	public function widget(){
	}
	
	public function attachment(){
		
	}
	
	public function attachment_fields($args){
		$options = get_option('cppress-options-attachment');
		if(!empty($options)){
			$optVal = isset($options[$args['name']]) ? $options[$args['name']] : '';
		}
		$element = '';
		if($args['tag'] == 'select' || $args['tag'] == 'selectmultiple'){
			$soptions = '';
			foreach($args['options'] as $key => $value){
				if(!is_array($value)){
					$soptions .= sprintf($this->formTags['selectoption'], 
							$key, selected($optVal, $key, false), $value);
				}else{
					$soptions .= sprintf($this->formTags['optiongroupstart'], $key);
					foreach($value as $k => $v){
						$optVal = is_array($optVal) ? $optVal : array($optVal);
						$selected = in_array($k, $optVal) ? 'selected="selected"' : '';
						$soptions .= sprintf($this->formTags['selectoption'], 
							$k, $selected, $v);
					}
					$soptions .= $this->formTags['optionsend'];
				}
			}
			$element = sprintf($this->formTags[$args['tag']], $args['id'], 'attachment', $args['name'], $soptions);
		}
		$this->assign('element', $element);
		$this->assign('args', $args); 
	}
	
	public function event(){
	}
	
}