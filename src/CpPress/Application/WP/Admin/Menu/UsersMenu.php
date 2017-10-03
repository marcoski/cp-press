<?php
namespace CpPress\Application\WP\Admin\Menu;

class UsersMenu extends Menu{
	
	public function add(Closure $closure){
		add_users_page(
			$this->titles['title'],
			$this->titles['menu'],
			$this->capability,
			$this->slug,
			$closure
		);
	}
	
}