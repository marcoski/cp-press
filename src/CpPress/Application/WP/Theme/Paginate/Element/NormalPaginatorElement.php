<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 11/10/16
 * Time: 10:47
 */

namespace CpPress\Application\WP\Theme\Paginate\Element;


class NormalPaginatorElement extends PaginatorElement {

	public function render(){
		return sprintf(
			$this->element,
			$this->all(),
			esc_url(get_pagenum_link($this->getPage())),
			$this->getPage()
		);
	}

}