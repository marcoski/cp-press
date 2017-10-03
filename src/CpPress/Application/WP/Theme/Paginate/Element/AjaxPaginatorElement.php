<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 11/10/16
 * Time: 10:49
 */

namespace CpPress\Application\WP\Theme\Paginate\Element;


class AjaxPaginatorElement extends PaginatorElement {

	public function __construct($page = null, array $attrs = array()) {
		parent::__construct($page, $attrs);
		$this->set('data-pagination-page', $page);
	}

	public function render(){
		return sprintf(
			$this->element,
			$this->all(),
			'#',
			$this->getPage()
		);
	}

}