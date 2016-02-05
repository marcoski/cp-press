<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\WP\Theme\Media\Image;
use CpPress\Application\FrontEndApplication;
use CpPress\Application\WP\Theme\Embed;
use Commonhelp\Util\OEmbed;

class CpWidgetGallery extends CpWidgetBase{
	
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
		$embed = $this->container->query('Embed');
		$gallery = array();
		$options = array(
    	'enablelightbox' => isset($instance['enablelightbox']) ? true : false,
    	'tperrow' => $instance['tperrow'] > 0 ? $instance['tperrow'] : 1,
			'title' => $instance['wtitle'],
			'galleryclass' => $instance['galleryclass']
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
					try{
						$gallery['items'][$i]['oembed'] = $embed->getEmbedObj($instance['items']['img_ext'][$i]);
					}catch(Exception $e){
						$gallery['items'][$i]['oembed'] = null;
					}
					$gallery['items'][$i]['isvideo'] = false;
				}else if($items[$i]['video']){
				}else if($instance['items']['video_ext'][$i]){
					$gallery['items'][$i]['link'] = $instance['items']['video_ext'][$i];
					$gallery['items'][$i]['oembed'] = $embed->getEmbedObj($instance['items']['video_ext'][$i]);
					$gallery['items'][$i]['isvideo'] = true;
				}
			}
		}
		if($instance['template']){
			$salt = md5(serialize($gallery).$options['wtitle']);
			$gid = $this->filter->apply(
				'cppress_widget_gallery_id', 'cppress-carousel-'. $salt , $gallery['items'], $options
			);
			$lid = $this->filter->apply(
					'cppress_widget_gallery_lightbox_id', 'cppress-carousel-lightbox-'.$salt, $gallery['items'], $options
			);
			$lightbox = '';
			if($options['enablelightbox']){
				$lightbox = FrontEndApplication::part('Gallery', 'lightbox', $this->container, array($gid, $lid, $gallery['items'][0], $options));
			}
			$galleryHtml = FrontEndApplication::part('Gallery', $instance['template'], $this->container, array($gid, $lid, $gallery, $options));
			$this->assign('galleryHtml', $galleryHtml);
			$this->assign('lightbox', $lightbox);
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
