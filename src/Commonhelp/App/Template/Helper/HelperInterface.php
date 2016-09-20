<?php
namespace Commonhelp\App\Template\Helper;

interface HelperInterface{
	
	/**
	 * Returns the canonical name of this helper.
	 *
	 * @return string The canonical name
	 */
	public function getName();
	
	/**
	 * Sets the default charset.
	 *
	 * @param string $charset The charset
	 */
	public function setCharset($charset);
	
	/**
	 * Gets the default charset.
	 *
	 * @return string The default charset
	*/
	public function getCharset();
	
}