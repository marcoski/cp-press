<?php
namespace CpPress\Application\WP\Admin;

use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionFactoryInterface;
use CpPress\Application\WP\Admin\Menu\Menu;

interface SettingsInterface{
	
	/**
	 * @return SettingsSectionFactoryInterface
	 */
	public function getSettingsSectionFactory();
	
	public function createSettingsSection();
	
	/**
	 * @return Menu
	 */
	public function getMenu();
	
	public function fields($tab);
	
}