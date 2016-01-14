<?php
namespace CpPress\Application\WP\Admin;

use Closure;
use CpPress\Application\WP\Admin\Menu\OptionsMenu;
use CpPress\Exception\SettingsException;
use CpPress\Application\WP\Admin\Options;

class Settings{
	
	private $name;
	private $group;
	
	private $errorSlug;
	
	private $menu;
	
	private $sections;
	private $fields;
	
	private $options;
	
	
	public function __construct($name, $group, OptionsMenu $menu, Options $options){
		$this->name = $name;
		$this->group = $group;
		$this->errorSlug = $name.'-error';
		$this->menu = $menu;
		$this->sections = array();
		$this->options = $options;
	}
	
	public function addOption($value){
		return $this->options->add($value);
	}
	
	public function updateOption($newValue){
		return $this->options->update($newValue);
	}
	
	public function deleteOption(){
		return $this->options->delete();
	}
	
	public function getOption($default=false){
		return $this->options->get($default);
	}
	
	public function getOptions(){
		return $this->options;
	}
	
	public function register(Closure $validate){
		register_setting($this->group, $this->name, $validate);
	}
	
	public function unregister($sanitize=''){
		unregister_setting($this->group, $this->name, $sanitize);
	}
	
	public function addField($id, $title, $section, Closure $renderize, $args=array()){
		if(array_key_exists($section, $this->sections)){
			$this->fields[$id] = true;
			add_settings_field($id, $title, $renderize, $this->menu->getSlug(), $section, $args);
		}
		
		throw new SettingsException('No section '.$section.' registered');
	}
	
	public function addSection($id, $title, Closure $renderize, Closure $addFields = null){
		$this->sections[$id] = true;
		if(!is_null($addFields)){
			$addFields($this);
		}
		add_settings_section($id, $title, $renderize, $this->menu->getSlug());		
	}
	
	public function fields(){
		settings_fields($this->group);
	}
	
	public function doSections(){
		do_settings_sections($this->menu->getSlug());
	}
	
	public function doFields($section){
		if(array_key_exists($section, $this->sections)){
			do_settings_fields($this->menu->getSlug(), $section);
		}
		
		throw new SettingsException('No section '.$section.' registered');
	}
	
	public function addError($field, $message, $type='error'){
		if(array_key_exists($field, $this->fields)){
			add_settings_error($field, $this->errorSlug, $message, $type);
		}
		
		throw new SettingsException('No field '.$field.' registered');
	}
	
	public function doError($sanitize=false, $hideOnUpdate=false){
		settings_errors($this->errorSlug, $sanitize, $hideOnUpdate);
	}
	
}