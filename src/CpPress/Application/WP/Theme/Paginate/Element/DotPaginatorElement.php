<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 11/10/16
 * Time: 11:01
 */

namespace CpPress\Application\WP\Theme\Paginate\Element;


class DotPaginatorElement extends PaginatorElement {


	public function render() {
		return '<li><span class="paginator-element-dot">...</span></li>';
	}

}