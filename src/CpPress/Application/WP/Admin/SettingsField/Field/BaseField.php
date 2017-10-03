<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

use CpPress\Application\WP\Admin\SettingsField\SettingsFieldInterface;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

abstract class BaseField implements SettingsFieldInterface{
	
	private $id;
	private $title;
	private $name;
	private $args = array();
	
	/**
	 * @var SettingsSectionInterface
	 */
	protected $section;
	
	protected $tags = array(
		'input' => '<input type="%s" id="%s" name="%s[%s]" value="%s" %s/>',
		'file' => '<input type="file" name="%s[%s]" id="%s" value="%s" %s/>',
		'select' => '<select id="%s" name="%s[%s]">%s</select>',
		'selectmultiple' => '<select id="%s" name="%s[%s][]" multiple>%s</select>',
		'checkbox' => '<input type="checkbox" id="%s" name="%s[%s]" value="%s" %s/>',
		'checkboxgroup' => '<input type="checkbox" id="%s" name="%s[%s][]" value="%s" %s/>',
		'radio' => '<input type="radio" name="%s[%s]" id="%s" value="%s" %s />',
		'radiogroup' => '<input type="radio" name="%s[%s][]" id="%s" value="%s" %s />',
		'optiongroupstart' => '<optgroup label="%s">',
		'optiongroupend' => '</optgroup>',
		'selectoption' => '<option value="%s" %s>%s</option>',
	);
	
	public function addField(){
		add_settings_field(
			$this->id, 
			$this->title, 
			array($this, 'render'),
			$this->section->getPage(),
			$this->section->getId(), 
			$this->args
		);
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
		return $this;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name = $name;
		return $this;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function setTitle($title){
		$this->title = $title;
		return $this;
	}
	
	public function getSection(){
		return $this->section;
	}
	
	public function setSection(SettingsSectionInterface $section){
		$this->section = $section;
		return $this;
	}
	
	public function setArgs(array $args){
		$this->args = $args;
		return $this;
	}
	
	public function get($arg){
		if($this->has($arg)){
			return $this->args[$arg];
		}
		
		return null;
	}
	
	public function has($arg){
		return isset($this->args[$arg]);
	}
	
	public function all(){
		return $this->args;
	}
	
	abstract public function render(array $args);
	
	abstract public function sanitize($inputs);
	
	abstract public function getType();
	
	protected function getFieldValue(){
		$options = get_option($this->section->getPage());
		if(!empty($options)){
			return isset($options[$this->name]) ? $options[$this->name] : null;
		}
		
		return null;
	}
	
	protected function renderAttrs($attrs){
		$attrs = '';
		if(!is_array($attrs)){
			$attrs = $attrs;
		}else{
			foreach($attrs as $name => $value){
				$attrs .= ' '.$name.'="'.$value.'"';
			}
		}
		
		return $attrs;
	}
	
}