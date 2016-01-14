<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
class CpWidgetMedia extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Media Widget', 'cppress'),
				array(
						'description' 	=> __('Aggregate Media', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-format-image';
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
		$media = BackEndApplication::part(
				'FieldsController', 'media_button', $this->container,
				array(
					array(
						'media' => $this->get_field_id( 'media' ),
						'external' => $this->get_field_id( 'external' )
					),
					array(
						'media' => $this->get_field_name( 'media' ),
						'external' => $this->get_field_name( 'external' ),
					),
					$instance['media'],
					$instance['external']
				)
		);
		$link = BackEndApplication::part(
				'FieldsController', 'link_button', $this->container,
				array(
					$this->get_field_id( 'desturi' ),
					$this->get_field_name( 'desturi' ),
					$instance['desturi']
				)
		);
		$this->assign('link', $link);
		$this->assign('media', $media);
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
