<?php
namespace CpPress\Application\WP\Admin\Menu;

use Closure;
class MediaMenu extends Menu{
	
	public function add(Closure $closure){
		add_media_page(
			$this->titles['title'],
			$this->titles['menu'],
			$this->capability,
			$this->slug,
			$closure
		);
	}
	
}