<?php
namespace CpPress\Application\WP\MetaType;

use CpPress\Exception\TaxonomyException;
use CpPress\Exception\CpPress\Exception;
class Taxonomy extends MetaType{
	
	private $postTypes = array();
	
	public function __construct($name, $postTypes = array()){
		parent::__construct($name);
		$this->labels = array_merge($this->labels, array(
			'update_item'					=> '',
			'new_item_name'					=> '',
			'parent_item'					=> '',
			'parent_item_colon'				=> '',
			'popular_items'					=> '',
			'separate_items_with_commas'	=> '',
			'add_or_remove_items'			=> '',
			'choose_from_most_used'			=> ''
		));
		$this->validParameters = array_merge($this->validParameters, array(
			'show_tagcloud', 'show_in_quick_edit', 'meta_box_cb', 'show_admin_column', 
			'update_count_callback', 'sort', 'singular_label'
		));
		if(!is_array($postTypes)){
			$postTypes = array($postTypes);
		}
		foreach($postTypes as $postType){
			if($postType instanceof PostType){
				$this->postTypes[] = $postType->getPostTypeName();
			}else if(is_string($postType) && in_array($postType, PostType::getPostTypes())){
				$this->postTypes[] = $postType;
			}else{
				throw new TaxonomyException('Invalide post type object');
			}
		}
	}
	
	
	public function register(){
		if(!in_array($this->name, $this->default)){
			$args = $this->args;
			$args['labels'] = $this->labels;
			return register_taxonomy($this->name, $this->postTypes, $args);
		}
		
		throw new TaxonomyException('Cannot register a defult taxonomy');
	}
	
}