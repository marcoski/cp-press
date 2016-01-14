<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
class CpWidgetText extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Text Box Widget', 'cppress'),
				array(
						'description' 	=> __('Free text box', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-text';
	}
	
	public function unwpautop($string) {
		$string = str_replace("<p>", "", $string);
		$string = str_replace(array("<br />", "<br>", "<br/>"), "\n", $string);
		$string = str_replace("</p>", "\n\n", $string);
		return $string;
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

	public function form($instance){
		$editor = BackEndApplication::part(
			'FieldsController', 'editor', $this->container,
			array(
				'cp-widget-editor'.$this->get_field_id( 'text' ),
				$instance['text'],
				array(
					'textarea_name' => $this->get_field_name('text'),
				)
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
		$this->assign('editor', $editor);
		return parent::form($instance);
	}



	/**
	 * Processing widget options on save
	 *
	 * @param array $new The new options
	 * @param array $old The previous options
	 */
	public function update($new, $old) {
		$new['text'] = $this->unwpautop($new['text']);
		return parent::update($new, $old);
	}

}
