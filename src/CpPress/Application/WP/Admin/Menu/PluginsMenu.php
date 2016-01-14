<?php
namespace CpPress\Application\WP\Admin\Menu;

class PluginsMenu extends Menu{
	
	public function add(Closure $closure){
		add_plugins_page(
			$this->titles['title'],
			$this->titles['menu'],
			$this->capability,
			$this->slug,
			$closure
		);
	}
}