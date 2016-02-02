<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\WP\Theme\Media\Image;
use CpPress\Application\FrontEndApplication;

class CpWidgetGallery extends CpWidgetBase{
	//dump(apply_filters('the_content', '[embed]http://www.youtube.com/watch?v=dQw4w9WgXcQ[/embed]'));
	//$post_embed = $wp_embed->run_shortcode('[embed]your-video-url[/embed]');
	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Gallery Widget', 'cppress'),
				array(
						'description' 	=> __('Aggregate Gallery', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-format-gallery';
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$gallery = array();
		$options = array(
    	'enablelightbox' => isset($instance['enablelightbox']) ? true : false,
    	'tperrow' => $instance['tperrow'] > 0 ? $instance['tperrow'] : 1
		);
		if($instance['items']['countitem'] > 0){
			for($i=0; $i<$instance['items']['countitem']; $i++){
				if($instance['items']['caption'][$i] !== ''){
					$gallery['items'][$i]['caption'] = $instance['items']['caption'][$i];
				}
				if($instance['items']['img'][$i] !== ''){
					$image = new Image();
					$image->set($instance['items']['img'][$i]);
					$imageSrc = $image->getImage($instance['items']['img'][$i]);
					$gallery['items'][$i]['link'] = $imageSrc[0];
					$gallery['items'][$i]['isvideo'] = false;
				}else if($instance['items']['img_ext'][$i]){
					$gallery['items'][$i]['link'] = $instance['items']['img_ext'][$i];
					$gallery['items'][$i]['isvideo'] = false;
				}else if($items[$i]['video']){
				}else if($instance['items']['video_ext'][$i]){
					$gallery['items'][$i]['link'] = $instance['items']['video_ext'][$i];
					$gallery['items'][$i]['isvideo'] = true;
				}
			}
		}
		if($instance['template']){
			$galleryHtml = FrontEndApplication::part('Gallery', $instance['template'], $this->container, array($gallery, $options));
			$this->assign('galleryHtml', $galleryHtml);
		}else{
			$this->assign('galleryHtml', '');
		}
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		$repeater = BackEndApplication::part(
				'FieldsController', 'repeater', $this->container,
				array(
						$this->get_field_id( 'items' ),
						$this->get_field_name( 'items' ),
						$instance['items'],
						array('add' => 'widget_gallery_add'),
						__('Media', 'cppress'),
						__('Element', 'cppress')
				)
		);
		$this->assign('repeater', $repeater);
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
