<?php
namespace CpPress\Application\Widgets;

class CpWidgetMenu extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Menu Widget', 'cppress'),
				array(
						'description' 	=> __('Aggregate Navigation menu', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-menu';
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		$menues = array();
		$tMenus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
		foreach($tMenus as $m){
			$menues[$m->term_id] = $m->name; 
		}
		$this->assign('menues', $menues);
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