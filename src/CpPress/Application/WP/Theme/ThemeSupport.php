<?php
namespace CpPress\Application\WP\Theme;

class ThemeSupport{
	
	public function addPostFormat(array $formats){
		add_theme_support('post-format', $formats);
	}
	
	public function setPostFormat($id, $format){
		set_post_format($id, $format);
	}
	
	public function hasPostFormat($id, $format){
		return has_post_format($format, $id);
	}
	
	public function getPostFormat($id){
		return get_post_format($id);
	}
	
	public function getPostFormatLink($format){
		return get_post_format_link($format);
	}
	
	public function getPostFormatSlug($format){
		return get_post_format_string($slug);
	}
	
	public function addPostThumbnails(array $postTypes = array()){
		if(empty($postTypes)){
			add_theme_support('post-thumbnails');
		}else{
			add_theme_support('post-thumbnails', $postTypes);
		}
	}
	
	public function hasPostThumbnails($id){
		return has_post_thumbnail($id);
	}
	
	public function showThumbnail($id, $size, $attr, $echo=true){
		if($echo){
			the_post_thumbnail($size, $attr);
		}else{
			return get_the_post_thumbnail($id, $size, $attr);
		}
	}
	
	public function addThumbnailSize($name, $width = 0, $height = 0, $crop = false){
		if(is_null($name) || $name === ''){
			set_post_thumbnail_size($width, $height, $crop);
		}else{
			add_image_size($name, $width, $height, $crop);
		}
	}
	
	public function getPostThumbnail($id){
		return get_post_thumbnail_id($id);
	}
	
	public function addFeedLinks(){
		add_theme_support( 'automatic-feed-links' ); 
	}
	
	public function addHtml5(array $tags){
		add_theme_support('html5', $tags);
	}
	
	public function addTitleTag(){
		add_theme_support('title-tag');
	}
	
	
	
}