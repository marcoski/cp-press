<?php
namespace CpPress\Application\WP\MetaType;

class CalendarTaxonomy extends Taxonomy{
	
	public function __construct(){
		parent::__construct('calendar', 'event');
		
		$this->labels = array(
			'name'							=> __('Calendars', 'cppress'),
			'singular_name'					=> __('Calendar', 'cppress'),
			'search_items'					=> __('Search Calendars', 'cppress'),
			'popular_items'					=> __('Popular Calendars', 'cppress'),
			'all_items'						=> __('All Calendars', 'cppress'),
			'parent_items'					=> __('Parent Calendars', 'cppress'),
			'parent_item_colon'				=> __('Parent Calendar:', 'cppress'),
			'edit_item'						=> __('Edit Calendar', 'cppress'),
			'update_item'					=> __('Update Calendar', 'cppress'),
			'add_new_item'					=> __('Add New Calendar', 'cppress'), 
			'new_item_name'					=> __('New Calendar Name', 'cppress'),
			'seperate_items_with_commas'	=> __('Seperate calendars with commas', 'cppress'),
			'add_or_remove_items'			=> __('Add or remove events', 'cppress'),
			'choose_from_the_most_used'		=> __('Choose from most used calendars', 'cppress')
		);
		
		$this->setHierarchical(true); 
		$this->setPublic(true);
		$this->setShowUi(true);
		$this->setQueryVar(true);
		$this->setLabel(__('Calendars', 'cppress'));
		$this->setSingularLabel(__('Calendar', 'cppress'));
	}
	
}