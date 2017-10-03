<?php
namespace CpPress\Application\WP\MetaType;

class NewsPostType extends PostType{
	
	public function __construct(){
		parent::__construct('news');
		
		$this->labels = array(
			'name'					=> _x('News', 'post type general name', 'cppress'),
			'singular_name'			=> _x('News', 'post type singular name', 'cppress'),
			'add_new'				=> __('Add New', 'cppress'),
			'add_new_item'			=> __('Add New News', 'cppress'),
			'edit_item'				=> __('Edit News', 'cppress'),
			'new_item'				=> __('New News', 'cppress'),
			'all_item'				=> __('All News', 'cppress'),
			'view_item'				=> __('View News', 'cppress'),
			'search_item'			=> __('Search News', 'cppress'),
			'not_found'				=> __('No news found', 'cppress'),
			'not_found_in_trash'	=> __('No news found in Trash', 'cppress'),
			'menu_name'				=> __('News', 'cppress')
		);
		
		$this->setPublic(true);
		$this->setHasArchive(false);
		$this->setTaxonomies(array());
		$this->setSupports(
			array(
				'title', 'editor', 'thumbnail', 'excerpt',
				'author', 'thumbnail', 'comments',
			)
		);
		/*$this->setCapabilities(array(
			'publish_news',
			'edit_news',
			'edit_others_news',
			'edit_published_news',
			'delete_published_news',
			'delete_news',
			'delete_others_news',
			'read_private_news',
			'manage_news_categories',
			'manage_news_tags'
		));*/
		
		$this->setMenuIcon('dashicons-book');
		
	}
	
}