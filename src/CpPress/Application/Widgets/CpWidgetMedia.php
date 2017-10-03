<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEnd\FieldsController;
use CpPress\Application\BackEndApplication;
use CpPress\Application\WP\Theme\Media\Image;

class CpWidgetMedia extends CpWidgetBase{

	private $wpQuery;
	
	/**
	 * @TODO auto embed video
	 * @see https://gist.github.com/joshhartman/5380593
	 */
	private $providers = array(
			'#https?://(www\.)?youtube.com/watch.*#i'            => array( 'http://www.youtube.com/oembed',                     true  ),
			'http://youtu.be/*'                                  => array( 'http://www.youtube.com/oembed',                     false ),
			'http://blip.tv/*'                                   => array( 'http://blip.tv/oembed/',                            false ),
			'#https?://(www\.)?vimeo\.com/.*#i'                  => array( 'http://vimeo.com/api/oembed.{format}',              true  ),
			'#https?://(www\.)?dailymotion\.com/.*#i'            => array( 'http://www.dailymotion.com/services/oembed',        true  ),
			'#https?://(www\.)?flickr\.com/.*#i'                 => array( 'http://www.flickr.com/services/oembed/',            true  ),
			'#https?://(.+\.)?smugmug\.com/.*#i'                 => array( 'http://api.smugmug.com/services/oembed/',           true  ),
			'#https?://(www\.)?hulu\.com/watch/.*#i'             => array( 'http://www.hulu.com/api/oembed.{format}',           true  ),
			'#https?://(www\.)?viddler\.com/.*#i'                => array( 'http://lab.viddler.com/services/oembed/',           true  ),
			'http://qik.com/*'                                   => array( 'http://qik.com/api/oembed.{format}',                false ),
			'http://revision3.com/*'                             => array( 'http://revision3.com/api/oembed/',                  false ),
			'http://i*.photobucket.com/albums/*'                 => array( 'http://photobucket.com/oembed',                     false ),
			'http://gi*.photobucket.com/groups/*'                => array( 'http://photobucket.com/oembed',                     false ),
			'#https?://(www\.)?scribd\.com/.*#i'                 => array( 'http://www.scribd.com/services/oembed',             true  ),
			'http://wordpress.tv/*'                              => array( 'http://wordpress.tv/oembed/',                       false ),
			'#https?://(.+\.)?polldaddy\.com/.*#i'               => array( 'http://polldaddy.com/oembed/',                      true  ),
			'#https?://(www\.)?funnyordie\.com/videos/.*#i'      => array( 'http://www.funnyordie.com/oembed',                  true  ),
			'#https?://(www\.)?twitter.com/.+?/status(es)?/.*#i' => array( 'http://api.twitter.com/1/statuses/oembed.{format}', true  ),
			'#https?://(www\.)?soundcloud\.com/.*#i'             => array( 'http://soundcloud.com/oembed',                      true  ),
			'#https?://(www\.)?slideshare.net/*#'                => array( 'http://www.slideshare.net/api/oembed/2',            true  ),
			'#http://instagr(\.am|am\.com)/p/.*#i'               => array( 'http://api.instagram.com/oembed',                   true  ),
	);
	
	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Media Widget', 'cppress'),
				array(
						'description' 	=> __('Aggregate Media', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-format-image';
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$embed = $this->container->query('Embed');
		if($instance['video'] != '' && filter_var($instance['video'], FILTER_VALIDATE_URL)){
			$this->assign('isVideo', true);
			$this->assign('isImage', false);
			try{
				$instance['oembed'] = $embed->getEmbedObj($instance['video']);
			}catch(Exception $e){
				$instance['link'] = $instance['video'];
				$instance['oembed'] = null;
			}
		}else if($instance['media'] != ''){
			$this->assign('isVideo', false);
			$this->assign('isImage', true);
			$image = new Image();
			$image->set($instance['media']);
			$imageSrc = $image->getImage($instance['media']);
			$instance['link'] = $imageSrc[0];
			$instance['oembed'] = null;
		}
		if($instance['desturi'] !== ""){
			if(!FieldsController::isLinkArgs($instance['desturi']) && filter_var($instance['desturi'], FILTER_SANITIZE_URL)){
			}else{
				$instance['desturi'] = FieldsController::getLinkPermalink($instance['desturi']);
			}
		}
		if(null !== $instance['desttaxonomy'] && $instance['desttaxonomy'] !== ""){
			if(!FieldsController::isLinkArgs($instance['desttaxonomy']) && filter_var($instance['desttaxonomy'], FILTER_SANITIZE_URL)){
			}else{
				$instance['desturi'] = FieldsController::getTaxonomyPermalink($instance['desttaxonomy']);
			}
		}
		unset($instance['external']);
		unset($instance['media']);
		$this->assignTemplate($instance, 'media_widget');
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		$media = BackEndApplication::part(
				'FieldsController', 'media_button', $this->container,
				array(
					array(
						'media' => $this->get_field_id( 'media' ),
						'external' => $this->get_field_id( 'external' )
					),
					array(
						'media' => $this->get_field_name( 'media' ),
						'external' => $this->get_field_name( 'external' ),
					),
					$instance['media'],
					$instance['external']
				)
		);
		$link = BackEndApplication::part(
				'FieldsController', 'link_button', $this->container,
				array(
					$this->get_field_id( 'desturi' ),
					$this->get_field_name( 'desturi' ),
					$instance['desturi']
				)
		);
		$taxonomy = BackEndApplication::part(
			'FieldsController', 'taxonomy_button', $this->container,
			array(
				$this->get_field_id( 'desttaxonomy' ),
				$this->get_field_name( 'desttaxonomy' ),
				$instance['desttaxonomy']
			)
		);
		$this->assign('link', $link);
		$this->assign('taxonomy', $taxonomy);
		$this->assign('media', $media);
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
