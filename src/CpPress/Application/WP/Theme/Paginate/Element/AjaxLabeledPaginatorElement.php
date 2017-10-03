<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 11/10/16
 * Time: 12:01
 */

namespace CpPress\Application\WP\Theme\Paginate\Element;


class AjaxLabeledPaginatorElement extends LabeledPaginatorElement  {


	public function __construct( $label, $page, array $attrs = array() ) {
		parent::__construct( $label, $page, $attrs );
		$this->set('data-pagination-page', $page);
	}

	public function render() {
		return sprintf(
			$this->element,
			$this->all(),
			'#',
			$this->label
		);
	}

}