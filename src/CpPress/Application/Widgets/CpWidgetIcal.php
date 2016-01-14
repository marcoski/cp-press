<?php
namespace CpPress\Application\Widgets;

class CpWidgetIcal extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Ical Widget', 'cppress'),
				array(
						'description' 	=> __('Ical calendar aggregator', 'cppress'),

						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-calendar';
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
		// outputs the options form on admin
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
