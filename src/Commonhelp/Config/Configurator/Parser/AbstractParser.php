<?php
namespace Commonhelp\Config\Configurator\Parser;

abstract class AbstractParser implements ParserInterface{
	
	/**
	 * Path to the config file
	 *
	 * @var string
	 */
	protected $path;
	
	/**
	 * Sets the path to the config file
	 *
	 * @param string $path
	 *
	 * @codeCoverageIgnore
	 */
	public function __construct($path){
		$this->path = $path;
	}
	
}