<?php
namespace CpPress\Application\WP\Admin\Menu;

class PostsMenu extends Menu{
	
	public function add(Closure $closure){
		add_posts_page(
			$this->titles['title'],
			$this->titles['menu'],
			$this->capability,
			$this->slug,
			$closure
		);
	}
	
}