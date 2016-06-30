<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\FrontEndApplication;
use CpPress\Application\BackEnd\PostController;
class CpWidgetLoop extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Loop Widget', 'cppress'),
				array(
						'description' 	=> __('Create a Loop', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-update';
		$this->adminScripts = array(
				array(
						'source' => 'cp-widget-loop',
						'deps' => array('jquery')
				)
		);
		$this->frontScripts = array(
				array(
						'source' => 'ajaxloop',
						'deps' => array('jquery')
				),
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$loop = FrontEndApplication::part('Post', 'loop', $this->container, array($instance));
		$paginate = '';
		if(isset($instance['paginate'])){
			$paginate = FrontEndApplication::part('Post', 'paginate', $this->container, array($instance));
		}
		$this->assign('paginate', $paginate);
		$this->assign('loop', $loop);
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		$instance = PostController::correctInstanceForCompatibility($instance);
		$advanced = BackEndApplication::part(
				'PostController', 
				'advanced', 
				$this->container, 
				array($this, $instance,  array('single' => false, 'show_view_options' => true)));
		$this->assign('advanced', $advanced);
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
