<?php
namespace Commonhelp\Config\Configurator\Parser;

use Commonhelp\Config\Configurator\Exception\ParseException;

class Json implements ParserInterface{
	
	/**
	 * {@inheritDoc}
	 * Loads a JSON file as an array
	 *
	 * @throws ParseException If there is an error parsing the JSON file
	 */
	public function parse($path){
		$data = json_decode(file_get_contents($path), true);
		if(json_last_error() !== JSON_ERROR_NONE){
			$error_message  = 'Syntax error';
			if(function_exists('json_last_error_msg')){
				$error_message = json_last_error_msg();
			}
			$error = array(
					'message' => $error_message,
					'type'    => json_last_error(),
					'file'    => $path,
			);
			throw new ParseException($error['message']);
		}
		return $data;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getSupportedExtensions(){
		return array('json');
	}
	
	public function write($path, array $configs){
		file_put_contents($path, json_encode($configs, JSON_PRETTY_PRINT));
	}
}