<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use Commonhelp\Util\Commonhelp\Util;

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
		$content = $instance['text'];
		if(isset($instance['removep']) && $instance['removep']){
			$content = wpautop($content);
		}else{
			$content = wpautop($content, false);
		}
		if(strpos($content, '[')){
			$content = do_shortcode($content);
		}
		if(strpos($content, 'cppress_addmailpoet_form')){
			$mpShortcode = $this->container->query('MailPoetShortcodeManager');
			$content = $mpShortcode->doShortcode($content);
		}
		$instance['text'] = $content;
		return parent::widget($args, $instance);
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
		return parent::update($new, $old);
	}

}
