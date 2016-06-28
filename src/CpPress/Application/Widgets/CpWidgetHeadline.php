<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\BackEnd\FieldsController;
class CpWidgetHeadline extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
			__('Headeline Widget', 'cppress'),
			array(
				'description' 	=> __('Create a headline', 'cppress'),
				'default_style' => 'simple'
			),
			array(),
			$templateDirs
		);
		$this->icon = 'dashicons-welcome-write-blog';
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$instance['font'] = FieldsController::getFont($instance['font']);
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance){
		$fArgs = array($this->get_field_id( 'font' ), $this->get_field_name( 'font' ), $instance['font']);
		$fonts = BackEndApplication::part('FieldsController', 'fonts', $this->container, $fArgs);
		$this->assign('fonts', $fonts);
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