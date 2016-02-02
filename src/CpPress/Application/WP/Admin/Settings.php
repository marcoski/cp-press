<?php
namespace CpPress\Application\WP\Admin;

use Closure;
use CpPress\Application\WP\Admin\Menu\OptionsMenu;
use CpPress\Exception\SettingsException;
use Commonhelp\WP\WPContainer;
use CpPress\Application\BackEndApplication;

class Settings{
	
	private $name;
	private $group;
	
	private $errorSlug;
	
	private $menu;
	
	private $sections;
	private $fields;
	
	private $options;
	
	private $container;
	
	
	public function __construct($name, $group, OptionsMenu $menu, WPContainer $container){
		$this->name = $name;
		$this->group = $group;
		$this->errorSlug = $name.'-error';
		$this->menu = $menu;
		$this->sections = array();
		
		$this->container = $container;
	}
	
	
	public function addFields(){
		$this->addField(
				'attachment-valid-mime',
				__('Valid Attachment', 'cppress'),
				'cppress-options-attachment',
				function($args){
					BackEndApplication::main('SettingsController', 'attachment_fields', $this->container, array($args));
				},
				array(
					'id' => 'attachment-valid-mime', 
					'name' => 'validmime', 
					'tag' => 'selectmultiple', 
					'options' => array(__('Mime Types', 'cppress') => array_flip(get_allowed_mime_types()))
				)
		);
	}
	
	public function addField($id, $title, $section, Closure $renderize, $args=array()){
		if(isset($this->sections[$section]) && $this->sections[$section]){
			$this->fields[$id] = true;
			add_settings_field($id, $title, $renderize, $section, $section, $args);
		}else{
			throw new SettingsException('No section '.$section.' registered');
		}
	}
	
	public function registerAll(){
		register_setting('cppress-options-general', 'cppress-options-general');
		register_setting('cppress-options-attachment', 'cppress-options-attachment');
		register_setting('cppress-options-widget', 'cppress-options-widget');
		register_setting('cppress-options-event', 'cppress-options-event');
	}
	
	public function addSections(){
		$this->addSection('cppress-options-general', __('General Settings', 'cppress'), function(){
			BackEndApplication::main('SettingsController', 'general', $this->container);
		});
		$this->addSection('cppress-options-widget', __('Widget Settings', 'cppress'), function(){
			BackEndApplication::main('SettingsController', 'widget', $this->container);
		});
		$this->addSection('cppress-options-event', __('Event Settings', 'cppress'), function(){
			BackEndApplication::main('SettingsController', 'event', $this->container);
		});
		$this->addSection('cppress-options-attachment', __('Attachment Settings', 'cppress'), function(){
			BackEndApplication::main('SettingsController', 'attachment', $this->container);
		});
	}
	
	public function addSection($id, $title, Closure $renderize){
		$this->sections[$id] = true;
		add_settings_section($id, $title, $renderize, $id);		
	}
	
	public function fields($tab = ''){
		if($tab == ''){
			$tab = $this->group;
		}
		settings_fields($tab);
	}
	
	public function doSections($tab = ''){
		if($tab == ''){
			$tab = $this->menu->getSlug();
		}
		do_settings_sections($tab);
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