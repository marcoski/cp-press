<?php
namespace CpPress\Application\WP\Admin\Menu;

use Closure;
class ManagementMenu extends Menu{
	
	public function add(Closure $closure){
		add_management_page(
			$this->titles['title'],
			$this->titles['menu'],
			$this->capability,
			$this->slug,
			$closure
		);
	}
	
}