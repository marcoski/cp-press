<?php
namespace CpPress\Application\WP\MetaType;

class EventPostType extends PostType{
	
	public function __construct(){
		parent::__construct('event');
		
		$this->labels = array(
			'name'					=> __('Events', 'cppress'),
			'singular_name'			=> __('Event', 'cppress'),
			'add_new'				=> __('Add New Event', 'cppress'),
			'add_new_item'			=> __('Add New Event', 'cppress'),
			'edit_item'				=> __('Edit Event', 'cppress'),
			'new_item'				=> __('New Event', 'cppress'),
			'all_item'				=> __('All Events', 'cppress'),
			'view_item'				=> __('View Event', 'cppress'),
			'search_item'			=> __('Search Event', 'cppress'),
			'not_found'				=> __('No events found', 'cppress'),
			'not_found_in_trash'	=> __('No events found in Trash', 'cppress'),
			'menu_name'				=> __('Events', 'cppress')
		);
		
		$this->setPublic(true);
		$this->setHasArchive(false);
		$this->setTaxonomies(array());
		$this->setCapabilityType(array('event', 'events'));
		$this->setMapMetaCap(true);
		$this->setSupports(array('title', 'editor', 'thumbnail', 'excerpt'));
		$this->setMenuIcon('dashicons-calendar');
		
	}
	
}