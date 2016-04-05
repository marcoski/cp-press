<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\BackEnd\FieldsController;
use CpPress\Application\WP\Query\Query;
use CpPress\Application\FrontEndApplication;

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
		$this->wpQuery = new Query();
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		if(isset($instance['enableadvanced'])){
			$queryArgs = array(
				'post_type'			=> 'post',
				'posts_per_page'	=> 1,
				'category__in'		=> isset($instance['categories']) ? $instance['categories'] : array(),
				'category__not_in' => isset($instance['excludecat']) ? $instance['excludecat'] : array(),
				'tag__in'			=> isset($instance['tags']) ? $instance['tags'] : array(),
				'tag_not_in' => isset($instance['excludetags']) ? $instance['excludetags'] : array(),
				'offset'			=> $instance['offset'],
				'order'				=> $instance['order'],
				'orderby'			=> $instance['orderby'],
				/* Set it to false to allow WPML modifying the query. */
				'suppress_filters' => false
			);
			unset($instance['enableadvanced']);
			unset($instance['categories']);
			unset($instance['tags']);
		}else if(isset($instance['postid']) && $instance['postid'] != ''){
			$queryArgs = FieldsController::getLinkArgs($instance['postid']);
			unset($instance['postid']);
		}
		unset($instance['offset']);
		unset($instance['order']);
		unset($instance['orderby']);
		unset($instance['limit']);
		$post = FrontEndApplication::part('Post', 'single', $this->container, array($queryArgs, $instance));
		$this->assign('post', $post);
		return parent::widget($args, $instance);
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
					'excludecat' => $this->get_field_id('excludecat'),
					'tags' => $this->get_field_id( 'tags' ),
					'excludetags' => $this->get_field_id('excludetags')
			),
			'name' => array(
					'enableadvanced' => $this->get_field_name( 'enableadvanced' ),
					'limit' => $this->get_field_name( 'limit' ),
					'offset' => $this->get_field_name( 'offset' ),
					'order' => $this->get_field_name( 'order' ),
					'orderby' => $this->get_field_name( 'orderby' ),
					'categories' => $this->get_field_name( 'categories' ),
					'excludecat' => $this->get_field_name('excludecat'),
					'tags' => $this->get_field_name( 'tags' ),
					'excludetags' => $this->get_field_name('excludetags')
			),
			'value' => $instance
		);
		$advanced = BackEndApplication::part('PostController', 'advanced', $this->container, array($advInstance, true));
		$posts = BackEndApplication::part(
				'FieldsController', 'link_button', $this->container,
				array(
						$this->get_field_id( 'postid' ),
						$this->get_field_name( 'postid' ),
						$instance['postid'],
						array('post')
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
