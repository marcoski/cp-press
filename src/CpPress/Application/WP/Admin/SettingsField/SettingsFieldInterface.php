<?php
namespace CpPress\Application\WP\Admin\SettingsField;

use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;

interface SettingsFieldInterface{
	
	public function getId();
	public function setId($id);
	
	public function getName();
	public function setName($name);
	
	public function getTitle();
	public function setTitle($title);
	
	public function getSection();
	public function setSection(SettingsSectionInterface $section);
	
	public function setArgs(array $args);
	
	public function get($arg);
	public function has($arg);
	public function all();
	
	public function addField();
	
	public function render(array $args);
	
	public function sanitize($inputs);
	
	public function getType();
	
}