<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 11/10/16
 * Time: 22:54
 */

namespace CpPress\Application\WP\Theme\Paginate;


use CpPress\Application\WP\Theme\Paginate\Element\AjaxLabeledPaginatorElement;
use CpPress\Application\WP\Theme\Paginate\Element\AjaxPaginatorElement;

class AjaxPaginator extends Paginator {

	public function getPaginatorElement() {
		return AjaxPaginatorElement::class;
	}

	public function getLabeledElement() {
		return AjaxLabeledPaginatorElement::class;
	}

	protected function getPagedVar() {
		if($this->query->has('paged')){
			return $this->query->get('paged');
		}

		return 1;
	}

}