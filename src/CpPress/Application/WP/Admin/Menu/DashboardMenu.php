<?php
namespace CpPress\Application\WP\Admin\Menu;

use Closure;
class DashboardMenu extends Menu{
	
	public function add(Closure $closure){
		add_dashboard_page(
			$this->titles['title'],
			$this->titles['menu'],
			$this->capability,
			$this->slug,
			$closure
		);
	}
	
} 
