<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\BackEnd\FieldsController;
use CpPress\Application\BackEnd\PostController;
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
		$this->adminScripts = array(
				array(
						'source' => 'cp-sliders',
						'deps' => array('jquery')
				)
		);
		$this->frontScripts = array(
				array(
						'source' => 'flexslider',
						'deps' => array('jquery')
				),
		);
		$this->frontStyles = array(
				array(
						'source' => 'flexslider'
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
		$type =  'image';
		if(!empty($instance['type']) && is_array($instance['type'])){
			$type = (array_pop(array_keys($instance['type'])));
		}
		$action = 'widget'.ucfirst($type);
		$options = array(
				'title' => $instance['wtitle'],
				'theme' => $instance['stheme'] == '' ? 'bootstrap' : $instance['stheme'],
				'speed' => $instance['speed'],
				'timeout' => $instance['timeout'],
				'navcolor' => $instance['navcolor'],
				'hideindicators' => isset($instance['hideindicators']) ? true : false,
				'hidecontrol' => isset($instance['hidecontrol']) ? true : false
		);
		$this->assign('options', $options);
		$slides = $this->$action($instance, $options);
		$slider = BackEndApplication::part('Slider', 'frontend_'.$type, $this->container, array($slides, $options));
		$this->assign('slider', $slider);
		return parent::widget($args, $instance);
	}
	
	private function widgetImage($instance){
		$embed = $this->container->query('Embed');
		$slides = $instance['slides']; $count = $instance['slides']['countitem'];
		$slider = array();
		for($i=0; $i<$count; $i++){
			if($slides['img'][$i] === "" && $slides['img_ext'][$i] !== ""){
				$slider[$i]['img'] = $embed->getEmbedObj($slides['img_ext'][$i]);
			}else{
				$slider[$i]['img'] = $slides['img'][$i];
			}
			$slider[$i]['title'] = $slides['title'][$i];
			$slider[$i]['content'] = $slides['content'][$i];
			$slider[$i]['link'] = array();
			if($slides['link'][$i] != ""){
				$slider[$i]['link']['url'] = $slides['link'][$i];
				if(filter_var($slider[$i]['link']['url'], FILTER_SANITIZE_URL)){
					$slider[$i]['link']['isext'] = true;
				}
				if(is_numeric($slider[$i]['link']['url'])){
					$slider[$i]['link']['isext'] = false;
					$slider[$i]['link']['url'] = get_permalink($slider[$i]['link']['url']);
					if(!$slider[$i]['link']['url']){
						$slider[$i]['link'] = array();
					}
				}
			}
			$slider[$i]['displaytitle'] = $slides['displaytitle'] == 1 ? true : false;
			$slider[$i]['displaycontent'] = $slides['displaycontent'] == 1 ? true : false;
		}
		return $slider;
	}
	
	private function widgetPost($instance){
		return $instance;
	}
	
	private function widgetSinglePost($instance){
		$posts = array();
		$postTypes = array();
		for($i=0; $i<$instance['posts']['countitem']; $i++){
			if(isset($instance['posts']['post'][$i])){
				$args = FieldsController::getLinkArgs($instance['posts']['post'][$i]);
				$posts[$i] = $args['p'];
				if(!in_array($args['post_type'], $postTypes)){
					$postTypes[$i] = $args['post_type'];
				} 
			}
		}
		return array(
			'title' => $instance['wtitle'],
			'args' => array(
				'post_type' => $postTypes,
				'post__in' => $posts
			),
			'countitem' => $instance['posts']['countitem']
		);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		$imageParams = array(
			array('id' => $this->get_field_id('slides'), 'name' => $this->get_field_name('slides')),
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
		$singlePostParams = array(
				array('id' => $this->get_field_id('singlepost'), 'name' => $this->get_field_name('singlepost')),
				$instance,
				$this->getRepeater(
						'posts',
						$instance['posts'],
						array('add' => 'widget_slider_add_singlepost'),
						array(
								'title' => __('Posts', 'cppress'),
								'item' => __('Post', 'cppress')
						)
				),
		);
		$image = BackEndApplication::part('SliderController', 'image', $this->container, $imageParams);
		$advanced = $this->getAdvPost($instance);
		$singlePost = BackEndApplication::part('SliderController', 'single_post', $this->container, $singlePostParams);
		$accordion = BackEndApplication::part(
			'FieldsController', 'accordion', $this->container,
			array(
				__('Sliders', 'cppress'),
				array(__('Image slider', 'cppress'), __('Posts slider', 'cppress'), __('Single post slider', 'cppress')),
				array('image', 'post', 'singlepost'),
				array($image, $advanced, $singlePost),
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
		$instance['post'] = PostController::correctInstanceForCompatibility($instance['post']);
		return BackEndApplication::part(
				'PostController', 
				'advanced', 
				$this->container, 
				array($this, $instance['post'], array('single' => false, 'show_view_options' => true)));
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
