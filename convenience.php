<?php
/**
 * @copyright    Copyright (C) Copyright (c) 2007 Marco Trognoni. All rights reserved.
 * @license        GNU/GPLv3, see LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
/*
 * Thanks to cake php convenicne function
 */
/**
 * Convenience method for echo().
 *
 * @param string $text String to echo
 */
	function e($text) {
		echo $text;
	}

/**
 * Convenience method for echo() with a line break.
 *
 * @param string $text String to echo
 */
	function en($text) {
		echo '<pre>'.$text.'</pre>';
	}


/**
 * Convenience method for strtolower().
 *
 * @param string $str String to lowercase
 */
	function low($str) {
		return strtolower($str);
	}
/**
 * Convenience method for strtoupper().
 *
 * @param string $str String to uppercase
 */
	function up($str) {
		return strtoupper($str);
	}
/**
 * Convenience method for str_replace().
 *
 * @param string $search String to be replaced
 * @param string $replace String to insert
 * @param string $subject String to search
 */
	function r($search, $replace, $subject) {
		return str_replace($search, $replace, $subject);
	}
/**
 * Print_r convenience function, which prints out <PRE> tags around
 * the output of given array. Similar to debug().
 *
 * @see	debug()
 * @param array	$var
 */
	function pr($var) {
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}
/**
 * Display parameter
 *
 * @param  mixed  $p Parameter as string or array
 * @return string
 */
function params($p) {
	if (!is_array($p) || count($p) == 0) {
		return null;
	} else {
		if (is_array($p[0]) && count($p) == 1) {
			return $p[0];
		} else {
			return $p;
		}
	}
}



/**
 * Read configuration directive
 *
 * @param string $section configuration directive section
 * @param string $name configuration directive name
 * @return string configuration value
 */
 function readConf($section, $name){
 	return Configurable::queryConf($section, $name);
 }

/**
 * 
 */
 function array_remove_keys($array, $key){
 	if(!is_array($key)){
 		return array_diff_key($array, array_flip(array($key)));
 	}else{
 		return array_diff_key($array, array_flip($key));
 	}
 }
 
 /**
  * 
  */
 function array_remove_keys_reverse($array, $keys){
 	$toReturn = array();
 	foreach($array as $k=>$v){
 		foreach($keys as $key){
 			if($v == $key){
 				$toReturn[$k] = $v;
 			}
 		}
 	}
 	
 	return $toReturn;
 }

?>