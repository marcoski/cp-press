<?php
namespace CpPress\Application\WP\MetaType;

use CpPress\Exception\MetaTypeException;
use Commonhelp\Util\Inflector;
abstract class MetaType{
	
	protected $validParameters = array(
		'public', 'label', 'labels', 'show_ui', 'show_in_menu', 'show_in_nav_menus',
		'query_var', 'rewrite', 'capabilities', 'description', 'hierarchical'
	);
	
	protected $default = array('post','page','dashboard','link','attachment', 'comment', 'category', 'post_tag', 'link_category', 'post_format');
	
	protected $labels = array(
		'name' 			=> '', 
		'singular_name'	=> '',
		'menu_name'		=> '',
		'all_items'		=> '',	
		'edit_item'		=> '',
		'view_item'		=> '',
		'add_new_item'	=> '',
		'search_items'	=> '',
		'not_found'		=> ''
	);
	
	protected $args;
	
	protected $name;
	
	public function __construct($name){
		$this->name = $name;
	}
	
	public function __get($name){
		$name = Inflector::underscore($name);
		if(array_key_exists($name, $this->args)){
			return $this->args[$name];
		}
	
		return null;
	}
	
	public function __set($name, $value){
		$name = Inflector::underscore($name);
		$this->args[$name] = $value;
	}
	
	/**
	 * Magic method to have any kind of setters or getters.
	 *
	 * @param string $name      Getter/Setter name
	 * @param array  $arguments Method arguments
	 *
	 * @return mixed
	 */
	public function __call($name, array $arguments){
		$prefix = strtolower(substr($name, 0, 3));
		$parameter = substr($name, 3);
		$pToCheck = Inflector::underscore($parameter);
		if(in_array($pToCheck, $this->validParameters)){
			if ($prefix === 'set' && isset($arguments[0])) {
				$this->$parameter = $arguments[0];
				return $this;
			} elseif ($prefix === 'get') {
				return $this->$parameter;
			}
		}
	
		throw new MetaTypeException('Invalid parameters '.$parameter.' mapped to '.$pToCheck);
	}
	
	public function setMetaTypeLabel($label, $value){
		if(array_key_exists($label, $this->labels)){
			$this->labels[$label] = $value;
		}
		
		throw new MetaTypeException('Invalid label '.$label);
	}
	
	public function getPostTypeName(){
		return $this->name;
	}
	
	abstract public function register();
}