<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 13/10/16
 * Time: 11:27
 */

namespace CpPress\Application\WP\Theme\Filter\Field;


use CpPress\Application\FrontEnd\FrontFilterController;
use CpPress\Application\FrontEndApplication;

class PostTagField extends AbstractField  {

	public function render() {
		$options = array();
		foreach(get_terms(array('taxonomy' => 'post_tag', 'hide_empty' => false)) as $i => $term){
			$options[$i]['value'] = $term->term_id;
			$options[$i]['label'] = $term->name;
		}
		$params = array(
			$options,
			$this->filter->apply('cppress_filter_post_tag_label', __('Tags', 'cppress')),
			array('tag__in' => array()),
			'post_tag'
		);
		return FrontEndApplication::part(FrontFilterController::class, 'dropdown', $this->application->getContainer(), $params);
	}

}