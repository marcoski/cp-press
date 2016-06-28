<?php
namespace CpPress\Application\WP\MetaType;

use CpPress\Exception\PostTypeException;
use CpPress\Exception\CpPress\Exception;
use CpPress\Application\WP\Admin\MetaBox;

class PostType extends MetaType{
	
	private $validSupports = array(
		'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 
		'comments','revisions', 'page-attributes', 'post-formats'
	);
	
	public function __construct($name){
		parent::__construct($name);
		$this->labels = array_merge($this->labels, array(
			'name_admin_bar' 	=> '', 
			'add_new'  			=> '', 
			'add_new_item' 		=> '', 
			'new_item' 			=> '',
			'not_found_in_trash' => '', 
			'parent_item_colon' => '' 
		));
		$this->validParameters = array_merge($this->validParameters, array(
			'exclude_from_search', 'publicly_queryable', 'show_in_admin_bar', 
			'menu_position', 'menu_icon', 'capability_type', 'map_meta_cap',
			'supports', 'register_meta_box_cb', 'taxonomies', 'has_archive', 
			'permalink_epmask', 'can_export'
		));
	}
	
	public static function exists($name){
		return post_type_exists($name);
	}
	
	public static function getPostTypes($args = array(), $output = 'names', $operator = 'and'){
		return get_post_types($args, $output, $operator);	
	}
	
	public static function getPostTypeObj($name){
		return get_post_type_object($name);
	}
	
	public function register(){
		if(!in_array($this->name, $this->default)){
			$args = $this->args;
			$args['labels'] = $this->labels;
			$return = register_post_type($this->name, $args);
			if($return instanceof \WP_Error){
				throw new PostTypeException($return->get_error_message());
			}
			return $return;
		}
		
		throw new PostTypeException('Canot register a defult post type');
	}
	
	public function unregister($slug=''){
		global $wp_post_types;
		if(isset( $wp_post_types[$this->name])) {
			unset($wp_post_types[$this->name]);
		}
	}
	
	public function addSupport($feature){
		if(is_array($feature)){
			if(in_array($feature, $this->validSupports)){
				return add_post_type_support($this->name, $feature);
			}
			
			throw new PostTypeException('Invalid feature for post type support '.$feature);
		}else if($feature instanceof MetaBox){
			$feature->setPostType($this);
			$feature->add();
		}else{
			throw new PostTypeException('Invalid feature for post type support '.$feature);
		}
	}
	
	public function removeSupport($feature){
		if(in_array($feature, $this->validSupports)){
			return remove_post_type_support($this->name, $feature);
		}
		
		throw new PostTypeException('Invalid feature for post type support '.$feature);
	}
	
	public function isSupport($feature){
		if(in_array($feature, $this->validSupports)){
			return post_type_supports($this->name, $feature);
		}
		
		return false;
	}
	
	public function setPost($id){
		set_post_type($id, $this->name);
	}
	
	public function getPost($id){
		return get_post_type($id);
	}
}