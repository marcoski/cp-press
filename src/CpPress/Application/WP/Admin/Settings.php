<?php
namespace CpPress\Application\WP\Admin;

use CpPress\Exception\SettingsException;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionFactoryInterface;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionFactory;
use CpPress\Application\WP\Admin\Menu\Menu;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

class Settings implements SettingsInterface{
	
	private $name;
	private $errorSlug;
	
	private $menu;
	
	/**
	 * @var SettingsSectionFactoryInterface
	 */
	private $settingsSectionFactory;
	
	public function __construct($name, Menu $menu, SettingsSectionFactoryInterface $settingsSectionFactory = null){
		$this->name = $name;
		$this->errorSlug = $name.'-error';
		$this->menu = $menu;
		$this->settingsSectionFactory = null !== $settingsSectionFactory ?: new SettingsSectionFactory();
	}
	
	public function getSettingsSectionFactory(){
		return $this->settingsSectionFactory;
	}
	
	public function createSettingsSection(){
		$this->settingsSectionFactory->create();
		foreach($this->settingsSectionFactory->all() as $section){
			$section->getSettingsFieldFactory()->create($section);
			if(count($section) > 0){
				$this->createSubSections($section);
			}
		}
		$this->registerAll();
	}
	
	public function getMenu(){
		return $this->menu;
	}
	
	public function registerAll(){
		foreach($this->settingsSectionFactory->all() as $section){
			register_setting(
				$section->getPage(),
				$section->getId(),
				array($section, 'sanitize')
			);
			if(count($section) > 0){
				$this->registerChildren($section);
			}
		}
	}
	
	public function fields($tab){
		settings_fields($tab);
	}
	
	private function createSubSections(SettingsSectionInterface $section){
		foreach($section as $child){
			$child->getSettingsFieldFactory()->create($child);
			$this->createSubSections($child);
		}
	}
	
	private function registerChildren(SettingsSectionInterface $section){
		foreach($section as $child){
			register_setting(
				$child->getPage(),
				$child->getId()
			);
			$this->registerChildren($child);
		}
	}
	
}