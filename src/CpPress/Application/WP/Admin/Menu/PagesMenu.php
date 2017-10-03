<?php
namespace CpPress\Application\WP\Admin\Menu;

class PagesMenu extends Menu{
	
	public function add(Closure $closure){
		add_pages_page(
			$this->titles['title'],
			$this->titles['menu'],
			$this->capability,
			$this->slug,
			$closure
		);
	}
	
}