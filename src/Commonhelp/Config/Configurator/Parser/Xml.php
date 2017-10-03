<?php
namespace Commonhelp\Config\Configurator\Parser;

use Commonhelp\Config\Configurator\Exception\ParseException;

class Xml implements ParserInterface{
	
	/**
	 * {@inheritDoc}
	 * Parses an XML file as an array
	 *
	 * @throws ParseException If there is an error parsing the XML file
	 */
	public function parse($path){
		libxml_use_internal_errors(true);
		$data = simplexml_load_file($path, null, LIBXML_NOERROR);
		if($data === false){
			$errors = libxml_get_errors();
			$latestError = array_pop($errors);
			$error = array(
				'message' => $latestError->message,
				'type'    => $latestError->level,
				'code'    => $latestError->code,
				'file'    => $latestError->file,
				'line'    => $latestError->line,
			);
			
			throw new ParseException($error);
		}
		$data = json_decode(json_encode($data), true);
		return $data;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getSupportedExtensions(){
		return array('xml');
	}
	
	public function write($path, array $configs){
	
	}
}