<?php
namespace CpPress\Application\WP\Walker;

use Walker;

class OnePageMenuWalker extends Walker{
	
	private $active;
	private $menu;
	public $filter = array();
	public $object = 'custom';
	
	public function __construct($active = -1, $menu='header-menu'){
		$this->active = $active;
		$this->menu = $menu;
	}
	
	public function setActive($active){
		$this->active = $active;
	}
	
	public function setMenu($menu){
		$this->menu = $menu;
	}
	
	public function start_lvl(&$output, $depth = 0, $args = Array()) {
		parent::start_lvl($output, $depth, $args);
	}
	
	public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		if(!empty($this->filter) && in_array($item->post_title, $this->filter)) return;
		if($item->object == $this->object && $args->theme_location == $this->menu){
			switch($this->object){
				case 'section':
					$item->url = '#'.$item->title;
				default:
					$item->url = get_bloginfo('url').DS.$item->url;
			}
		}
		if($item->object != $this->object){
			$item->classes[] = 'noonepage';
		}
		if($this->active > 0 && $this->active == $item->ID){
			$item->classes[] = 'active';
			$item->classes[] = 'current';
		}
		parent::start_el($output, $item, $depth, $args, $id);
	}
	
	public function end_el(&$output, $object, $depth = 0, $args = Array()) {
		parent::end_el($output, $object, $depth, $args);
	}
}