<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
class CpWidgetGallery extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Gallery Widget', 'cppress'),
				array(
						'description' 	=> __('Aggregate Gallery', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-format-gallery';
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		// outputs the content of the widget
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		$repeater = BackEndApplication::part(
				'FieldsController', 'repeater', $this->container,
				array(
						$this->get_field_id( 'items' ),
						$this->get_field_name( 'items' ),
						$instance['items'],
						array('add' => 'widget_gallery_add'),
						__('Media', 'cppress'),
						__('Element', 'cppress')
				)
		);
		$this->assign('repeater', $repeater);
		return parent::form($instance);
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update($new_instance, $old_instance) {
		return parent::update($new_instance, $old_instance);
	}
}
