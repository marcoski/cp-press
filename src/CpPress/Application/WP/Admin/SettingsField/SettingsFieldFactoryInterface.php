<?php
namespace CpPress\Application\WP\Admin\SettingsField;

use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

interface SettingsFieldFactoryInterface{
	
	public function create(SettingsSectionInterface $section);
	
	public function get($field);
	
	public function has($field);
	
	public function all();
	
}