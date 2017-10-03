<?php
namespace CpPress\Application\WP\Admin\SettingsSection;

interface SettingsSectionInterface extends \ArrayAccess, \Traversable, \Countable{
	
	public function addSection();
	
	public function getId();
	
	public function setId($id);
	
	public function getTitle();
	
	public function setTitle($title);
	
	public function getPage();
	
	public function setPage($page);
	
	public function render();
	
	public function sanitize(array $inputs);
	
	public function getSettingsFieldFactory();
	
	public function get($id);
	
	public function add(SettingsSectionInterface $child);
	
	public function remove($id);
	
	public function has($id);
	
	public function all();
	
	public function getParent();
	
	public function setParent(SettingsSectionInterface $parent = null);
	
	public function getRoot();
	
	public function isRoot();
	
}