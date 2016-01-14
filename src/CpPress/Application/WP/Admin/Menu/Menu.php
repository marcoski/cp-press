<?php
namespace CpPress\Application\WP\Admin\Menu;

use Closure;
use Commonhelp\Ssh\System\PublicKey;

class Menu{
	
	protected $titles;
	protected $capability;
	protected $slug;
	
	protected $position;
	protected $icon;
	
	public function __construct(array $titles, $capability, $slug){
		list($this->titles['title'], $this->titles['menu']) = $titles;
		$this->capability = $capability;
		$this->slug = $slug;
		$this->icon = '';
		$this->position = null;
	}
	
	public function setPosition($position){
		$this->position = $position;
	}
	
	public function getPosition(){
		return $this->position;
	}
	
	public function setIcon($icon){
		$this->icon = $icon;
	}
	
	public function getIcon(){
		return $this->icon;
	}
	
	public function getTitle($title='title'){
		if(array_key_exists($title, $this->titles)){
			return $this->titles[$key];
		}
		
		return '';
	}
	
	public function getSlug(){
		return $this->slug;
	}
	
	public function getCapability(){
		return $this->capability;
	}
	
	public function add(Closure $closure){
		if(!is_null($position)){
			add_menu_page(
				$this->titles['title'], 
				$this->titles['menu'], 
				$this->capability, 
				$this->slug,
				$closure,
				$this->icon,
				$this->position
			);
		}else{
			add_object_page(
				$this->titles['title'],
				$this->titles['menu'],
				$this->capability,
				$this->slug,
				$closure,
				$this->icon
			);
		}
	}
	
	public function addSub(Menu $menu, Closure $closure){
		add_submenu_page(
			$this->slug,
			$menu->getTitle(), 
			$menu->getTitle('menu'), 
			$menu->getCapability(), 
			$menu->getSlug(),
			$closure
		);
	}
	
	public function remove(){
		remove_menu_page($this->slug);
	}
	
	public function removeSub(Menu $menu){
		remove_submenu_page($this->slug, $menu->getSlug());
	}

}