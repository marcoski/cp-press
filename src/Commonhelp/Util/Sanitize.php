<?php
namespace Commonhelp\Util;


class Sanitize{
	
	public static $uris =  array('xmlns', 'profile', 'href', 'src', 'cite', 'classid', 'codebase', 'data', 'usemap', 'longdesc', 'action');
	
	/**
	 * Checks and cleans a URL.
	 *
	 * @param string $url The URL to be cleaned.
	 * @return string The cleaned $url.
	 */
	public static function url($url){
		if('' == $url){
			return $url;
		}
		
		$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
		$strip = array('%0d', '%0a', '%0D', '%0A');
		$url = self::deepReplace($strip, $url);
		$url = str_replace(';//', '://', $url);
		/* If the URL doesn't appear to contain a scheme, we
		 * presume it needs http:// appended (unless a relative
		 * link starting with /, # or ? or a php file).
		*/
		if (strpos($url, ':') === false && ! in_array($url[0], array('/', '#', '?')) &&
				! preg_match('/^[a-z0-9-]+?\.php/i', $url)){
				$url = 'http://' . $url;
		}
				
		
		$url = str_replace('&amp;', '&#038;', $url);
		$url = str_replace("'", '&#039;', $url);
		return $url;
	}
	
	public static function noNull($string){
		$string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string);
		return preg_replace('/\\\\+0+/', '', $string);
	}
	
	public static function jsEntities($string){
		return preg_replace('%\s*\{[^}]*(\}\s*;?|$)%', '', $string);
	}
	
	public static function htmlOneAttr($string, $element){
		$string = self::noNull($string);
		$string = self::jsEntities($string);
		$matches = array();
		preg_match('/\s*/', $string, $matches);
		$lead = $matches[0];
		preg_match('/\s*$/', $string, $matches);
		$trail = $matches[0];
		if(empty($trail)){
			$string = substr($string, strlen($lead));
		}else{
			$string = substr($string, strlen($lead), -strlen($trail));
		}
		
		$split = preg_split('/\s*=\s*/', $string, 2);
		$name = $split[0];
		if(count($split) ==  2){
			$value = $split[1];
			if($value == ''){
				$quote = '';
			}else{
				$quote = $value[0];
			}
			if($quote == '"' || $quote == '"'){
				if(substr($value, -1) != $quote){
					return '';
				}
				
				$value = substr($value, 1, -1);
			}else{
				$quote = '"';
			}
			
			if(in_array(strtolower($name), self::$uris)){
				$value = self::url($value);
			}
			
			$string = "$name=$quote$value$quote";
		}
		
		return $lead . $string . $trail;
		
	}
	
	public static function htmlAttrParse($element){
		$valid = preg_match('%^(<\s*)(/\s*)?([a-zA-Z0-9]+\s*)([^>]*)(>?)$%', $element, $matches);
		if(!$valid){
			return false;
		}
	
		$begin = $matches[1];
		$slash = $matches[2];
		$elname = $matches[3];
		$attr = $matches[4];
		$end = $matches[5];
	
		if($slash !== ''){
			return false;
		}
	
		if(preg_match('%\s*/\s*$%', $attr, $matches)){
			$xhtmlSlash = $matches[0];
			$attr = substr($attr, 0, -strlen($xhtmlSlash));
		}else{
			$xhtmlSlash = '';
		}
		
		$attrArray = self::htmlHairParse($attr);
		if($attrArray === false){
			return false;
		}
		
		array_unshift($attrArray, $begin . $slash . $elname);
		array_push($attrArray, $xhtmlSlash . $end);
		
		return $attrArray;
	}
	
	public static function htmlHairParse($attr){
		if($attr === ''){
			return array();
		}
		
		$regex =
		'(?:'
		.     '[-a-zA-Z:]+'   // Attribute name.
		. '|'
			.     '\[\[?[^\[\]]+\]\]?' // Shortcode in the name position implies unfiltered_html.
		. ')'
		. '(?:'               // Attribute value.
		.     '\s*=\s*'       // All values begin with '='
		.     '(?:'
		.         '"[^"]*"'   // Double-quoted
		.     '|'
		.         "'[^']*'"   // Single-quoted
		.     '|'
		.         '[^\s"\']+' // Non-quoted
		.         '(?:\s|$)'  // Must have a space
		.     ')'
		. '|'
		.     '(?:\s|$)'      // If attribute has no value, space is required.
		. ')'
		. '\s*';              // Trailing space is optional except as mentioned above.

		// Although it is possible to reduce this procedure to a single regexp,
		// we must run that regexp twice to get exactly the expected result.
		
		$validation = "%^($regex)+$%";
		$extraction = "%$regex%";
		
		if(preg_match($validation, $$attr)){
			preg_match_all($extraction, $attr, $attrArray);
			return $attrArray[0];
		}else{
			return false;
		}
	}
	
	public static function htmlSplit($input){
		return preg_split(self::htmlSplitRegex(), $input, -1, PREG_SPLIT_DELIM_CAPTURE);
	}
	
	public static function htmlSplitRegex(){
		$comments =
		'!'           // Start of comment, after the <.
		. '(?:'         // Unroll the loop: Consume everything until --> is found.
		.     '-(?!->)' // Dash not followed by end of comment.
		.     '[^\-]*+' // Consume non-dashes.
		. ')*+'         // Loop possessively.
		. '(?:-->)?';   // End of comment. If not found, match all input.
		
		$cdata =
		'!\[CDATA\['  // Start of comment, after the <.
		. '[^\]]*+'     // Consume non-].
		. '(?:'         // Unroll the loop: Consume everything until ]]> is found.
		.     '](?!]>)' // One ] not followed by end of comment.
		.     '[^\]]*+' // Consume non-].
		. ')*+'         // Loop possessively.
		. '(?:]]>)?';   // End of comment. If not found, match all input.
		
		$escaped =
		'(?='           // Is the element escaped?
		.    '!--'
		. '|'
		.    '!\[CDATA\['
		. ')'
		. '(?(?=!-)'      // If yes, which type?
		.     $comments
		. '|'
		.     $cdata
		. ')';
		
		$regex =
		'/('              // Capture the entire match.
		.     '<'           // Find start of element.
		.     '(?'          // Conditional expression follows.
		.         $escaped  // Find end of escaped element.
		.     '|'           // ... else ...
		.         '[^>]*>?' // Find end of normal element.
		.     ')'
		. ')/';
		
		return $regex;
	}
	
	
	
	/**
	 * Perform a deep string replace operation to ensure the values in $search are no longer present
	 *
	 * Repeats the replacement operation until it no longer replaces anything so as to remove "nested" values
	 * e.g. $subject = '%0%0%0DDD', $search ='%0D', $result ='' rather than the '%0%0DD' that
	 * str_replace would return
	 *
	 * @access private
	 *
	 * @param string|array $search
	 * @param string $subject
	 * @return string The processed string
	 */
	private static function deepReplace($search, $subject){
		$found = true;
		$subject = (string) $subject;
		while ($found){
			$found = false;
			foreach ((array) $search as $val){
				while (strpos( $subject, $val) !== false){
					$found = true;
					$subject = str_replace($val, '', $subject);
				}
			}
		}
		
		return $subject;
	}
	
}