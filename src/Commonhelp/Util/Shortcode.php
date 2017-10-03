<?php
namespace Commonhelp\Util;

use Commonhelp\Util\Collections\ArrayCollection;

class Shortcode extends ArrayCollection{
	
	public function addShortcode($tag, \Closure $closure){
		if(trim($tag) == ''){
			throw new \RuntimeException('Invalid shortcode name: Empty given');
		}
		if(preg_match('@[<>&/\[/]\x00-\x20=]@', $tag)){
			throw new \RuntimeException('Invalid shortcode name: ' . $tag . 'Do not use spaces or reserved characters & / < > [ ]');
		}
		
		$this[$tag] = $closure;
	}
	
	public function has($content, $tag){
		if(!strpos($content, '[')){
			return false;
		}
		
		if($this->exists($tag)){
			preg_match_all('/' . $this->getRegex() . '/', $content, $matches, PREG_SET_ORDER);
			if(empty($matches)){
				return false;
			}
			
			foreach($matches as $shortcode){
				if($tag === $shortcode[2]){
					return true;
				}else if(!empty($shortcode[5]) && $this->has($shortcode[5], $tag)){
					return true;
				}
			}
		}
		
		return false;
	}
	
	protected function getRegex(array $tagNames = array()){
		if(empty($tagNames)){
			$tagNames = array_keys($this);
		}
		
		$tagRegexp = join('|', array_map('preg_quote', $tagNames));
		return
		'\\['                              // Opening bracket
		. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
		. "($tagRegexp)"                     // 2: Shortcode name
		. '(?![\\w-])'                       // Not followed by word character or hyphen
		. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
		.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
		.     '(?:'
		.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
		.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
		.     ')*?'
		. ')'
		. '(?:'
		.     '(\\/)'                        // 4: Self closing tag ...
		.     '\\]'                          // ... and closing bracket
		. '|'
		.     '\\]'                          // Closing bracket
		.     '(?:'
		.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
		.             '[^\\[]*+'             // Not an opening bracket
		.             '(?:'
		.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
		.                 '[^\\[]*+'         // Not an opening bracket
		.             ')*+'
		.         ')'
		.         '\\[\\/\\2\\]'             // Closing shortcode tag
		.     ')?'
		. ')'
		. '(\\]?)';
	}
	
	public function doShortcode($content, $ignoreHtml = false){
		if(strpos($content, '[') === false){
			return $content;
		}
		
		if(empty($this)){
			return $content;
		}
		
		preg_match_all('@\[([a-zA-Z0-9_-]++)@', $content, $matches);
		$tagNames = array_intersect(array_keys($this->toArray()), $matches[1]);
		if(empty($tagNames)){
			return $content;
		}
		
		$content = $this->doShortcodeInHtml($content, $ignoreHtml, $tagNames);
		$pattern = $this->getRegex($tagNames);
		$content = preg_replace_callback("/$pattern/", function($m){
			return $this->doShortCodeTag($m);
		}, $content);
		
		return $content;
		
	}
	
	protected function doShortcodeInHtml($content, $ignoreHtml, $tagNames){
		$trans = array('&#91;' => '&#091;', '&#93;' => '&#093;');
		$content = strtr($content, $trans);
		$trans = array('[' => '&#91;', ']' => '&#93;');
		
		$pattern = $this->getRegex($tagNames);
		$textArray = Sanitize::htmlSplit($content);
		foreach($textArray as &$element){
			if($element == '' || $element[0] !== '<'){
				continue;
			}
			
			$noopen = false === strpos($element, '[');
			$noclose = false === strpos($element, ']');
			if($noopen || $noclose){
				if($noopen xor $noclose){
					$element = strtr($element, $trans);
				}
				continue;
			}
			
			if($ignoreHtml 
					|| substr($element, 0, 4) === '<!--' 
					|| substr($element, 0, 9) === '<![CDATA['){
				$element = strtr($element, $trans);
				continue;
			}
			
			$attributes = Sanitize::htmlAttrParse($element);
			if($attributes === false){
				if(preg_match('%^<\s*\[\[?[^\[\]]+\]%', $element)){
					$element = preg_replace_callback("/$pattern/", function($m){
						return $this->doShortCodeTag($m);
					}, $element);
				}
				
				$element = strtr($element, $trans);
				continue;
			}
			
			$front = array_shift($attributes);
			$back = array_pop($attributes);
			$matches = array();
			preg_match('%[a-zA-Z0-9]+%', $front, $matches);
			$elName = $matches[0];
			
			foreach($attributes as &$attr){
				$open = strpos($attr, '[');
				$close = strpos($attr, ']');
				if($open === false || $close === false){
					continue;
				}
				
				$double = strpos($attr, '"');
				$single = strpos($attr, "'");
				if( ($single === false || $open < $single) &&
						($double === false || $open < $double) ){
					$attr = preg_replace_callback("/$pattern/", function($m){
						return $this->doShortCodeTag($m);
					}, $attr);
				}else{
					$count = 0;
					$newAttr = preg_replace_callback("/$pattern/", function($m){
						return $this->doShortCodeTag($m);
					}, $attr, -1, $count);
					if($count > 0){
						$newAttr = Sanitize::htmlOneAttr($newAttr, $elname);
						if(trim($newAttr) !==  ''){
							$attr = $newAttr;
						}
					}
				}
			}
			
			$element = $front . implode('', $attributes) . $back;
			$element = strtr($element, $trans);
		}
		$content = implode('', $textArray);
		
		return $content;
	}
	
	protected function doShortCodeTag($m){
		if($m[1] == '[' && $m[6] == ']'){
			return substr($m[0], 1, -1);
		}
		
		$tag = $m[2];
		$attr = $this->parseAtts($m[3]);
		
		if(isset($m[5])){
			return $m[1] . call_user_func($this[$tag], $attr, $m[5], $tag) . $m[6];
		}else{
			return $m[1] . call_user_func($this[$tag], $attr, null, $tag) . $m[6];
		}
	}
	
	protected function getAttsRegex(){
		return '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
	}
	
	public function parseAtts($text){
		$atts = array();
		$pattern = $this->getAttsRegex();
		$text = preg_replace("/[\x{00a0}\x{200b}]+/u", "", $text);
		if(preg_match_all($pattern, $text, $match, PREG_SET_ORDER)){
			foreach($match as $m){
				if(!empty($m)){
					$atts[strtolower($m[1])] = stripcslashes($m[2]);
				}else if(!empty($m[3])){
					$atts[strtolower($m[3])] = stripcslashes($m[4]);
				}else if(!empty($m[5])){
					$atts[strtolower($m[5])] = stripcslashes($m[6]);
				}else if(isset($m[7]) && strlen($m[7])){
					$atts[] = stripcslashes($m[7]);
				}else if(isset($m[8])){
					$atts[] = stripcslashes($m[8]);
				}
			}
			
			foreach($atts as &$value){
				if(false !== strpos($value, '<')){
					if(1 !== preg_match('/*[*<]*+(?:<[^>]*+>[^<]*+)*+$/', $value)){
						$value = '';
					}
				}
			}
		}else{
			$atts = ltrim($text);
		}
		
		return $atts;
	}
	
	public function getAtts($pairs, $atts){
		$atts = (array)$atts;
		$out = array();
		foreach($pairs as $name => $default){
			if(array_key_exists($name, $atts)){
				$out[$name] = $atts[$name];
			}else{
				$out[$name] = $default;
			}
		}
		
		return $out;
	}
	
	
}