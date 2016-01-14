<?php
namespace CpPress\Application\WP\Admin\Menu;

use Closure;
class CommentsMenu extends Menu{

	public function add(Closure $closure){
		add_comments_page(
			$this->titles['title'], 
			$this->titles['menu'], 
			$this->capability, 
			$this->slug,
			$closure
		);
	}
	
}