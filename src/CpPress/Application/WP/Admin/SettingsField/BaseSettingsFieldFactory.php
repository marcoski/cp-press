<?php
namespace CpPress\Application\WP\Admin\SettingsField;

use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

abstract class BaseSettingsFieldFactory implements SettingsFieldFactoryInterface{
	
	/**
	 * 
	 * @var SettingsField[];
	 */
	protected $fields;
	
	abstract public function create(SettingsSectionInterface $section);
	
	public function get($field){
		if($this->has($field)){
			return $this->fields[$field];
		}
		
		return null;
	}
	
	public function has($field){
		return isset($this->fields[$field]);
	}
	
	public function all(){
		return $this->fields;
	}
	
	protected function add(){
		foreach((array) $this->fields as $field){
			$field->addField();
		}
	}
	
}