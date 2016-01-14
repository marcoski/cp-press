<?php
namespace CpPress\Application\WP\Admin\Menu;

use Closure;
class OptionsMenu extends Menu{
	
	public function add(Closure $closure){
		add_options_page(
			$this->titles['title'],
			$this->titles['menu'],
			$this->capability,
			$this->slug,
			$closure
		);
	}
	
}