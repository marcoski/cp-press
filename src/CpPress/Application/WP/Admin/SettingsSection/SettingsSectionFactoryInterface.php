<?php
namespace CpPress\Application\WP\Admin\SettingsSection;

interface SettingsSectionFactoryInterface{
	
	public function create();
	
	public function get($section);
	
	public function has($section);
	
	public function all();
	
	public function render($section);
	
}