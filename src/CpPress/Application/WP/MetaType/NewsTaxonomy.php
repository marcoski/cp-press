<?php
namespace CpPress\Application\WP\MetaType;

class NewsTaxonomy extends Taxonomy{
	
	public function __construct(){
		parent::__construct('news-category', 'news');
		
		$this->labels = array(
			'name' => _x('News Categories', 'taxonomy general name', 'cppress'),
			'singular_name' => _x('News Category', 'taxonomy singular name', 'cppress'),
			'search_items' =>  __('Search News Categories', 'cppress'),
			'all_items' => __('All News Categories', 'cppress'),
			'parent_item' => __('Parent News Category', 'cppress'),
			'parent_item_colon' => __('Parent News Category:', 'cppress'),
			'edit_item' => __('Edit News Category', 'cppress'),
			'view_item' => __('View News Category', 'cppress'),
			'update_item' => __('Update News Category', 'cppress'),
			'add_new_item' => __('Add New News Category', 'cppress'),
			'new_item_name' => __('New News Category Name', 'cppress'),
			'menu_name' => __('Categories', 'cppress')
		);
		$this->setHierarchical(true); 
		$this->setPublic(true);
		$this->setShowUi(true);
		$this->setShowAdminColumn(true);
		$this->setQueryVar(true);
		$this->setLabel(__('News Categories', 'cppress'));
		$this->setSingularLabel(__('News Category', 'cppress'));
		$this->setRewrite(array(
			'slug' => $slug,
			'with_front' => false,
			'hierarchical' => false
		));
	}
	
}