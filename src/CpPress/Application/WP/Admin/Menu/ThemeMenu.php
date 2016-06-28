<?php
namespace CpPress\Application\WP\Admin\Menu;

class ThemeMenu extends Menu{
	
	public function add(Closure $closure){
		add_theme_page(
			$this->titles['title'],
			$this->titles['menu'],
			$this->capability,
			$this->slug,
			$closure
		);
	}
	
}