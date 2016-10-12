<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\BackEnd\FieldsController;
class CpWidgetButton extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
			__('Button Widget', 'cppress'),
			array(
				'description' 	=> __('Create a button', 'cppress'),
				'default_style' => 'simple'
			),
			array(),
			$templateDirs
		);
		$this->icon = 'dashicons-sticky';
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		if(!filter_var($instance['link'], FILTER_VALIDATE_URL)){
			$instance['link'] = FieldsController::getLinkPermalink($instance['link']);
		}
		if(!filter_var($instance['taxonomy'], FILTER_VALIDATE_URL)){
			$instance['link'] = FieldsController::getLinkPermalink($instance['taxonomy']);
		}
		$styles = array(
			'background-color' => $instance['bcolor'] != '' ? $instance['bcolor'] : null,
			'color' => $instance['bcolor'] != '' ? $instance['tcolor'] : null,
			'font-size' => $instance['fsize'] != '' ? $instance['fsize'] . 'em' : null,
			'border-radius' => $instance['rounding'] > 0 ? $instance['rounding'] . 'em' : null,
			'padding' => $instance['padding'] != '' ? $instance['padding'] . 'em ' . $instance['padding']*2 . 'em' : null,
		);
		$style = $this->filter->apply('cppress_widget_button_style', $this->formatStyles($styles), $instance['wtitle']);
		$this->assign('style', $style);
		
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance){
		$link = BackEndApplication::part(
				'FieldsController', 'link_button', $this->container,
				array(
						$this->get_field_id( 'link' ),
						$this->get_field_name( 'link' ),
						$instance['link'],
				)
		);
		$taxonomy = BackEndApplication::part(
			'FieldsController', 'taxonomy_button', $this->container,
			array(
				$this->get_field_id( 'taxonomy' ),
				$this->get_field_name( 'taxonomy' ),
				$instance['taxonomy'],
			)
		);
		$icon = BackEndApplication::part(
				'FieldsController', 'icon_button', $this->container,
				array(
						array(
								'icon' => $this->get_field_id( 'icon' ),
								'color' => $this->get_field_id( 'iconcolor' ),
								'class' => $this->get_field_id( 'iconclass' ),
						),
						array(
								'icon' => $this->get_field_name( 'icon' ),
								'color' => $this->get_field_name( 'iconcolor' ),
								'class' => $this->get_field_name( 'iconclass' ),
						),
						$instance['icon'],
						$instance['iconcolor'],
						$instance['iconclass'],
						true
				)
		);
		$this->assign('icon', $icon);
		$this->assign('link', $link);
		$this->assign('taxonomy', $taxonomy);
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