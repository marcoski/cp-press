<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
class CpWidgetPost extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
			__('Post Widget', 'cppress'),
			array(
				'description' 	=> __('Aggregate Post', 'cppress'),
				'default_style' => 'simple'
			),
			array(),
			$templateDirs
		);
		$this->icon = 'dashicons-admin-post';
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
	public function form($instance){
		$advInstance = array(
			'id' => array(
					'enableadvanced' => $this->get_field_id( 'enableadvanced' ),
					'limit' => $this->get_field_id( 'limit' ),
					'offset' => $this->get_field_id( 'offset' ),
					'order' => $this->get_field_id( 'order' ),
					'orderby' => $this->get_field_id( 'orderby' ),
					'categories' => $this->get_field_id( 'categories' ),
					'tags' => $this->get_field_id( 'tags' )
			),
			'name' => array(
					'enableadvanced' => $this->get_field_name( 'enableadvanced' ),
					'limit' => $this->get_field_name( 'limit' ),
					'offset' => $this->get_field_name( 'offset' ),
					'order' => $this->get_field_name( 'order' ),
					'orderby' => $this->get_field_name( 'orderby' ),
					'categories' => $this->get_field_name( 'categories' ),
					'tags' => $this->get_field_name( 'tags' )
			),
			'value' => $instance
		);
		$advanced = BackEndApplication::part('PostController', 'advanced', $this->container, array($advInstance, true));
		$posts = BackEndApplication::part('PostController', 'select', $this->container,
			array(
					$this->get_field_name( 'postid' ),
					$this->get_field_id( 'postid' ),
					$instance['postid']
			)
		);
		$this->assign('post_list', $posts);
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
