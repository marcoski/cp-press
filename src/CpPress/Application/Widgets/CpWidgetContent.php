<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\BackEnd\FieldsController;
use CpPress\Application\WP\Query\Query;
use CpPress\Application\FrontEndApplication;
use CpPress\Application\BackEnd\PostController;

class CpWidgetContent extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
			__('Content Widget', 'cppress'),
			array(
				'description' 	=> __('Aggregate Content', 'cppress'),
				'default_style' => 'simple'
			),
			array(),
			$templateDirs
		);
		$this->icon = 'dashicons-admin-post';
		$this->wpQuery = new Query();
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$post = FrontEndApplication::part('Post', 'single', $this->container, array($instance));
		$this->assign('post', $post);
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance){
		$instance = PostController::correctInstanceForCompatibility($instance);
		$advanced = BackEndApplication::part(
				'PostController', 
				'advanced', 
				$this->container, 
				array($this, $instanced, array('single' => true, 'show_view_options' => true)
			)
		);
		$posts = BackEndApplication::part(
				'FieldsController', 'link_button', $this->container,
				array(
						$this->get_field_id( 'postid' ),
						$this->get_field_name( 'postid' ),
						$instance['postid'],
				)
		);
		$this->assign('post_list', $posts);
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
