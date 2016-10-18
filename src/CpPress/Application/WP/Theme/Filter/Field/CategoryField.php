<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 13/10/16
 * Time: 11:33
 */

namespace CpPress\Application\WP\Theme\Filter\Field;


use CpPress\Application\FrontEnd\FrontFilterController;
use CpPress\Application\FrontEndApplication;

class CategoryField extends AbstractField {

	public function render() {
		$options = array();
		foreach(get_terms(array('taxonomy' => 'category', 'hide_empty' => false)) as $i => $term){
			$options[$i]['value'] = $term->term_id;
			$options[$i]['label'] = $term->name;
		}
		$params = array(
			$options,
			$this->filter->apply('cppress_filter_category_label', __('Category', 'cppress')),
			array('category__in' => array()),
			'category'
		);
		return FrontEndApplication::part(FrontFilterController::class, 'dropdown', $this->application->getContainer(), $params);
	}

}