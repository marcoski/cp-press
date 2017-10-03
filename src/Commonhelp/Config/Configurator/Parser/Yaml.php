<?php
namespace Commonhelp\Config\Configurator\Parser;

use Symfony\Component\Yaml\Yaml as YamlParser;
use Commonhelp\Config\Configurator\Exception\ParseException;

class Yaml implements ParserInterface{
	
	/**
	 * {@inheritDoc}
	 * Loads a YAML/YML file as an array
	 *
	 * @throws ParseException If If there is an error parsing the YAML file
	 */
	public function parse($path){
		try{
			$data = YamlParser::parse(file_get_contents($path));
		}catch(Exception $exception){
			throw new ParseException(
					array(
							'message'   => 'Error parsing YAML file',
							'exception' => $exception,
					)
			);
		}
		return $data;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getSupportedExtensions(){
		return array('yaml', 'yml');
	}
	
	public function write($path, array $configs){
	
	}
}