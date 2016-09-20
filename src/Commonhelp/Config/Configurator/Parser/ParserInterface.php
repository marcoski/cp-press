<?php
namespace Commonhelp\Config\Configurator\Parser;

interface ParserInterface{
	
	/**
	 * Parses a file from `$path` and gets its contents as an array
	 *
	 * @param  string $path
	 *
	 * @return array
	 */
	public function parse($path);
	
	/**
	 * Returns an array of allowed file extensions for this parser
	 *
	 * @return array
	*/
	public function getSupportedExtensions();
	
	public function write($path, array $configs);
	
}