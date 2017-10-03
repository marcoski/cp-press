<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 11/10/16
 * Time: 11:04
 */

namespace CpPress\Application\WP\Theme\Paginate\Element;


class LabeledPaginatorElement extends PaginatorElement {

	protected $label;

	public function __construct($label, $page = null, array $attrs = array() ) {
		parent::__construct( $page, $attrs );
		$this->label = $label;
	}

	public function getLabel(){
		return $this->label;
	}

	public function setLabel($label){
		$this->label = $label;
		return $this;
	}

	public function render() {
		return sprintf(
			$this->element,
			$this->all(),
			esc_url(get_pagenum_link($this->getPage())),
			$this->label
		);
	}

}