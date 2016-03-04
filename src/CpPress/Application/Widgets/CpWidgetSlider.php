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
	
	private function widgetParallax($instance){
		$slides = $instance['parallax']; $count = $instance['parallax']['countitem'];
		$slider = array();
		for($i=0; $i<$count; $i++){
			$slider['slide'][$i] = $slide['slides'][$i];
		}
		$slider['subtitle'] = $slides['subtitle'];
		$slider['displaytitle'] = $slides['displaytitle'] == 1 ? true : false;
		$slider['displayoverlay'] = $slides['displayoverlay'] == 1 ? true : false;
		$slider['nextlink'] = $slides['nextlink'];
		$slider['bg'] = $slides['img'];
		return $slider;
	}
	
	private function widgetPost($instance){
		return $instance;
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
		$parallaxParams = array(
			array('id' => $this->get_field_id('parallax'), 'name' => $this->get_field_name('parallax')),
			$instance['parallax'],
			$this->getRepeater(
				'parallax',
				$instance['parallax'],
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
		$advanced = $this->getAdvPost($instance);
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
					'posttype' => $this->get_field_id('posttype'),
					'limit' => $this->get_field_id( 'limit' ),
					'offset' => $this->get_field_id( 'offset' ),
					'order' => $this->get_field_id( 'order' ),
					'orderby' => $this->get_field_id( 'orderby' ),
					'categories' => $this->get_field_id( 'categories' ),
					'tags' => $this->get_field_id( 'tags' ),
					'linktitle' => $this->get_field_id('linktitle'),
					'showinfo' => $this->get_field_id('showinfo'),
					'showexcerpt' => $this->get_field_id('showexcerpt'),
					'showthumbnail' => $this->get_field_id('showthumbnail'),
					'hidecontent' => $this->get_field_id('hidecontent'),
					'linkthumbnail' => $this->get_field_id('linkthumbnail'),
					'postspercolumn' => $this->get_field_id('postspercolumn')
			),
			'name' => array(
					'posttype' => $this->get_field_name('post').'[posttype]',
					'limit' => $this->get_field_name( 'post' ).'[limit]',
					'offset' => $this->get_field_name( 'post' ).'[offset]',
					'order' => $this->get_field_name( 'post' ).'[order]',
					'orderby' => $this->get_field_name( 'post' ).'[orderby]',
					'categories' => $this->get_field_name( 'post' ).'[categories]',
					'tags' => $this->get_field_name( 'post' ).'[tags]',
					'linktitle' => $this->get_field_name('post').'[linktitle]',
					'showinfo' => $this->get_field_name('post').'[showinfo]',
					'showexcerpt' => $this->get_field_name('post').'[showexcerpt]',
					'showthumbnail' => $this->get_field_name('post').'[showthumbnail]',
					'hidecontent' => $this->get_field_name('post').'[hidecontent]',
					'linkthumbnail' => $this->get_field_name('post').'[linkthumbnail]',
					'postspercolumn' => $this->get_field_name('post').'[postspercolumn]'
					
			),
			'value' => $instance['post'],
		);
		return BackEndApplication::part('PostController', 'advanced', $this->container, array($adv, false, true));
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
