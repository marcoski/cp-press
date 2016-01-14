<?php
namespace CpPress\Application\WP\MetaType;

class PagePostType extends PostType{
	
	public function __construct(){
		parent::__construct('page');
	}
	
}