<?php
namespace Commonhelp\Config\Configurator\Parser;

use Commonhelp\Config\Configurator\Exception\ParseException;

class Ini implements ParserInterface{
	
	/**
	 * {@inheritDoc}
	 * Parses an INI file as an array
	 *
	 * @throws ParseException If there is an error parsing the INI file
	 */
	public function parse($path){
		$data = @parse_ini_file($path, true);
		if(!$data){
			$error = error_get_last();
			throw new ParseException($error);
		}
		return $data;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getSupportedExtensions(){
		return array('ini');
	}
	
	public function write($path, array $configs){
		
	}
	
}