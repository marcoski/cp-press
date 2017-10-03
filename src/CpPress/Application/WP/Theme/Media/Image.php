<?php
namespace CpPress\Application\WP\Theme\Media;

class Image extends Media{

	public function __construct($id=-1){
		parent::__construct($id, 'image');
	}
	
	public function getImage($offset, $size=''){
		if($size == ''){
			$size = 'full';
		}
		return wp_get_attachment_image_src($this->media[$offset]->ID, $size);
	}
}