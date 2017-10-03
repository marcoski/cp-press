<?php
namespace Commonhelp\DI\Factory;

interface RequestedEntryInterface{
	
	
	/**
	 * Returns the name of the entry that was requested by the container.
	 * 
	 * @return string
	 */
	public function getName();
	
}