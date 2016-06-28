<?php
namespace CpPress\Application\WP\MetaType;

class EventtagsTaxonomy extends Taxonomy{
	
	public function __construct(){
		parent::__construct('event-tags', 'event');
		
		$this->labels = array(
			'name'							=> __('Event Tags', 'cppress'),
			'singular_name'					=> __('Event Tag', 'cppress'),
			'search_items'					=> __('Search Event Tags', 'cppress'),
			'popular_items'					=> __('Popular Event Tags', 'cppress'),
			'all_items'						=> __('All Event Tags', 'cppress'),
			'parent_items'					=> __('Parent Event Tags', 'cppress'),
			'parent_item_colon'				=> __('Parent Event Tag:', 'cppress'),
			'edit_item'						=> __('Edit Event Tag', 'cppress'),
			'update_item'					=> __('Update Event Tag', 'cppress'),
			'add_new_item'					=> __('Add New Event Tag', 'cppress'),
			'new_item_name'					=> __('New Event Tag Name', 'cpress'),
			'seperate_items_with_commas'	=> __('Seperate event tags with commas', 'cppress'),
			'add_or_remove_items'			=> __('Add or remove events', 'cppress'),
			'choose_from_the_most_used'		=> __('Choose from most used event tags', 'cppress')
		);
		
		$this->setHierarchical(true); 
		$this->setPublic(true);
		$this->setShowUi(true);
		$this->setQueryVar(true);
		$this->setLabel(__('Event Tags', 'cppress'));
		$this->setSingularLabel(__('Event Tag', 'cppress'));
	}
	
}