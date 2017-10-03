<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
class CpWidgetSocialbutton extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Social Media Button Widget', 'cppress'),
				array(
						'description' 	=> __('A Social media button widget', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-share';
		$this->adminScripts = array(
			array(
				'source' => 'cp-social-media-buttons',
				'deps' => array('jquery')		
			)
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$styles = array(
				'font-size' => $instance['isize'] != '' ? $instance['isize'] . 'em' : null,
				'border-radius' => $instance['rounding'] > 0 ? $instance['rounding'] . 'em' : null,
				'padding' => $instance['padding'] > 0 ? $instance['padding'] . 'em' : null,
				'margin' => $instance['margin'] > 0 ? 
					$instance['margin'] . 'em ' . $instance['margin'] . 'em '  . $instance['margin'] . 'em'
					: null,
		);
		for($i = 0; $i<$instance['networks']['countitem']; $i++){
			$styles['color'] = $instance['networks']['icolor'][$i] != '' ? $instance['networks']['icolor'][$i] : null;
			$styles['background-color'] = $instance['networks']['bgcolor'][$i] != '' ? $instance['networks']['bgcolor'][$i] : null;
			$network = array(
				'icon' => $instance['networks']['network'][$i],
				'url' => $instance['networks']['url'][$i],
				'style' => $this->formatStyles($styles)
			);
			$instance['networks'][$i] = $network;
		}
		unset($instance['networks']['network']);
		unset($instance['networks']['countitem']);
		unset($instance['networks']['url']);
		unset($instance['networks']['bgcolor']);
		unset($instance['networks']['icolor']);
		return parent::widget($args, $instance);
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
						$this->get_field_id( 'networks' ),
						$this->get_field_name( 'networks' ),
						$instance['networks'],
						array(
								'add' => 'widget_social_add',
						),
						__('Network', 'cppress'),
						__('Select network', 'cppress')
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