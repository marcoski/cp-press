<?php
namespace CpPress\Application\WP\MetaType;

class NewstagsTaxonomy extends Taxonomy{
	
	public function __construct(){
		parent::__construct('news-tags', 'news');
		
		$this->labels = array(
			'name' => _x('News Tags', 'taxonomy general name', 'cppress'),
			'singular_name' => _x('News Tag', 'taxonomy singular name', 'cppress'),
			'search_items' =>  __('Search News Tags', 'cppress'),
			'popular_items' => __('Popular News Tags', 'cppress'),
			'all_items' => __('All News Tags', 'cppress'),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __('Edit News Tag', 'cppress'), 
			'update_item' => __('Update News Tag', 'cppress'),
			'add_new_item' => __('Add New News Tag', 'cppress'),
			'new_item_name' => __('New News Tag Name', 'cppress'),
			'separate_items_with_commas' => __('Separate news tags with commas', 'cppress'),
			'add_or_remove_items' => __('Add or remove news tags', 'cppress'),
			'choose_from_most_used' => __('Choose from the most used news tags', 'cppress'),
			'menu_name' => __('Tags', 'cppress'),
		);
		
		$this->setHierarchical(true); 
		$this->setPublic(true);
		$this->setShowUi(true);
		$this->setQueryVar(true);
		$this->setLabel(__('News Tags', 'cppress'));
		$this->setSingularLabel(__('News Tag', 'cppress'));
		$this->setRewrite(array(
			'slug' => 'news-tags',
			'with_front' => false,
			'hierarchical' => false
		));
	}
	
}