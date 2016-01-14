<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
class CpWidgetSlider extends CpWidgetBase{

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Slider Widget', 'cppress'),
				array(
						'description' 	=> __('Aggregate Slider', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-media-interactive';
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
	public function form($instance) {
		$imageParams = array(
			array('id' => $this->get_field_id('images'), 'name' => $this->get_field_name('images')),
			$instance,
			$this->getRepeater(
				'slides',
				$instance['slides'],
				array('add' => 'widget_slider_add_images'),
				array(
						'title' => __('Slides', 'cppress'),
						'item' => __('Slide', 'cppress')
				)
			),	
		);
		$parallaxParams = array(
			array('id' => $this->get_field_id('parallax'), 'name' => $this->get_field_name('parallax')),
			$instance['parallax'],
			$this->getRepeater(
				'sentences',
				$instance['sentences'],
				array('add' => 'widget_slider_add_sentences'),
				array(
					'title' => __('Slides', 'cppress'),
					'item' => __('Slide', 'cppress')
				)
			),
			$this->getMedia($instance['parallax'], 'parallax')	
		);
		$image = BackEndApplication::part('SliderController', 'image', $this->container, $imageParams);
		$parallax = BackEndApplication::part('SliderController', 'parallax', $this->container, $parallaxParams);
		$advanced = $this->getAdvPost($in);
		$accordion = BackEndApplication::part(
			'FieldsController', 'accordion', $this->container,
			array(
				__('Sliders', 'cppress'),
				array(__('Image slider', 'cppress'), __('Parallax slider', 'cppress'), __('Post slider', 'cppress')),
				array('image', 'parallax', 'post'),
				array($image, $parallax, $advanced),
				array(
					'name' => $this->get_field_name('type'),
					'value' => $instance['type'] != '' ? $instance['type'] : array()
				)
			)
		);
		$this->assign('accordion', $accordion);
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
	
	private function getMedia($instance, $type){
		return BackEndApplication::part(
			'FieldsController', 'media_button', $this->container,
			array(
				array(
						'media' => $this->get_field_id( 'img_'.$type ),
						'external' => $this->get_field_id( 'img_ext_'.$type )
				),
				array(
						'media' => $this->get_field_name( $type ).'[img]',
						'external' => $this->get_field_name( $type ).'[img_ext]',
				),
				$instance['img'],
				$instance['img_ext']
			)
		);
	}
	
	private function getAdvPost($instance){
		$adv = array(
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
					'enableadvanced' => $this->get_field_name( 'post' ).'[enableadvanced]',
					'limit' => $this->get_field_name( 'post' ).'[limit]',
					'offset' => $this->get_field_name( 'post' ).'[offset]',
					'order' => $this->get_field_name( 'post' ).'[order]',
					'orderby' => $this->get_field_name( 'post' ).'[orderby]',
					'categories' => $this->get_field_name( 'post' ).'[categories]',
					'tags' => $this->get_field_name( 'post' ).'[tags]'
			),
			'value' => $instance['post']
		);
		return BackEndApplication::part('PostController', 'advanced', $this->container, array($adv, false));
	}
	
	private function getRepeater($type, $instance, $actions, $labels){
		return BackEndApplication::part(
			'FieldsController', 'repeater', $this->container,
			array(
				$this->get_field_id( $type ),
				$this->get_field_name( $type ),
				$instance,
				$actions,
				$labels['title'],
				$labels['item']
			)
		);
	}

}
