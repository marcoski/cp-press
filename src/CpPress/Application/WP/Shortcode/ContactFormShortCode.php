<?php
namespace CpPress\Application\WP\Shortcode;

use Commonhelp\App\Http\Request;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Hook\Filter;

class ContactFormShortcode{
	
	private $type;
	private $baseType;
	private $name;
	private $options = array();
	private $rawValues = array();
	private $labels = array();
	private $attr = '';
	private $values = array();
	private $content = '';
	
	private $request;
	private $filter;
	
	public function __construct($tag, Request $request, Filter $filter=null){
		foreach($tag as $key => $value){
			if(property_exists(__CLASS__, $key)){
				$this->{$key} = $value;
			}
		}
		$this->request = $request;
		$this->filter = $filter;
	}
	
	public function __get($name){
		$validProperties = array('type', 'baseType', 'name', 'options', 'rawValues', 'values', 'labels', 'attr', 'content');
		if(property_exists(__CLASS__, $name) && in_array($name, $validProperties)){
			return $this->$name;
		}
	
		return null;
	}
	
	public static function sanitizeQueryVar($text){
		$text = wp_unslash($text);
		$text = wp_check_invalid_utf8($text);
		if (false !== strpos($text, '<')) {
			$text = wp_pre_kses_less_than($text);
			$text = wp_strip_all_tags($text);
		}
		$text = preg_replace('/%[a-f0-9]{2}/i', '', $text);
		$text = preg_replace('/ +/', ' ', $text);
		$text = trim($text, ' ');
		return $text;
	}
	
	public function getParam($key, $default=null){
		return $this->request->getParam($key, $default);
	}
	
	public function isRequired(){
		return ('*' == substr($this->type, -1));
	}
	
	public function hasOption($opt){
		$pattern = sprintf('/^%s(:.+)?$/i', preg_quote($opt, '/'));
		return (bool) preg_grep($pattern, $this->options);
	}
	
	public function getOption($opt, $pattern = '', $single = false){
		$presetPatterns = array(
				'date' => '([0-9]{4}-[0-9]{2}-[0-9]{2}|today(.*))',
				'int' => '[0-9]+',
				'signed_int' => '-?[0-9]+',
				'class' => '[-0-9a-zA-Z_]+',
				'id' => '[-0-9a-zA-Z_]+'
		);
	
		if(isset($presetPatterns[$pattern])){
			$pattern = $presetPatterns[$pattern];
		}
	
		if('' == $pattern){
			$pattern = '.+';
		}
	
		$pattern = sprintf('/^%s:%s$/i', preg_quote($opt, '/'), $pattern);
	
		if($single){
			$matches = $this->getFirstMatchOption($pattern);
			if(!$matches){
				return false;
			}
				
			return substr($matches[0], strlen($opt) + 1);
		}else{
			$matchesAll = $this->getAllMatchOptions($pattern);
			if(!$matchesAll){
				return false;
			}
				
			$results = array();
			foreach($matchesAll as $matches){
				$results[] = substr($matches[0], strlen($opt) + 1);
			}
				
			return $results;
		}
	}
	
	public function getFirstMatchOption($pattern){
		foreach((array) $this->options as $option){
			if(preg_match($pattern, $option, $matches)){
				return $matches;
			}
		}
	
		return false;
	}
	
	public function getAllMatchOptions($pattern){
		$result = array();
		foreach((array) $this->options as $option){
			if(preg_match($pattern, $option, $matches)){
				$result[] = $matches;
			}
		}
	
		return $result;
	}
	
	public function getIdOption() {
		return $this->getOption('id', 'id', true);
	}
	
	public function getClassOptions($default=''){
		if(is_string($default)){
			$default = explode(' ', $default);
		}
		$options = array_merge(
				(array) $default,
				(array) $this->getOption('class', 'class'));
		$options = array_filter(array_unique($options));
		return implode(' ', $options);
	}
	
	public function getSizeOption($default=''){
		$option = $this->getOption('size', 'int', true);
		if ($option){
			return $option;
		}
	
		$matchesAll = $this->getAllMatchOptions( '%^([0-9]*)/[0-9]*$%' );
		foreach ((array) $matchesAll as $matches) {
			if(isset($matches[1]) && '' !== $matches[1]){
				return $matches[1];
			}
		}
	
		return $default;
	}
	
	public function getMaxLengthOption($default=''){
		$option = $this->getOption('maxlength', 'int', true);
		if($option){
			return $option;
		}
		$matchesAll = $this->getAllMatchOptions('%^(?:[0-9]*x?[0-9]*)?/([0-9]+)$%');
		foreach((array) $matchesAll as $matches){
			if(isset($matches[1]) && '' !== $matches[1]){
				return $matches[1];
			}
		}
	
		return $default;
	}
	
	public function getMinLengthOption($default=''){
		$option = $this->getOption( 'minlength', 'int', true );
		if($option){
			return $option;
		}
	
		return $default;
	}
	
	public function getRowsOption($default=''){
		$matchesAll = $this->getAllMatchOptions('%^([0-9]*)x([0-9]*)(?:/[0-9]+)?$%');
		foreach((array) $matchesAll as $matches){
			if (isset( $matches[2] ) && '' !== $matches[2]){
				return $matches[2];
			}
		}
	
		return $default;
	}
	
	public function getColsOption($default=''){
		$matchesAll = $this->getAllMatchOptions('%^([0-9]*)x([0-9]*)(?:/[0-9]+)?$%');
		foreach((array) $matchesAll as $matches){
			if(isset( $matches[1] ) && '' !== $matches[1]){
				return $matches[1];
			}
		}
	
		return $default;
	}
	
	public function getDateOption($opt){
		$option = $this->getOption($opt, 'date', true);
		if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $option)){
			return $option;
		}
		if(preg_match('/^today(?:([+-][0-9]+)([a-z]*))?/', $option, $matches)){
			$number = isset($matches[1]) ? (int) $matches[1] : 0;
			$unit = isset($matches[2]) ? $matches[2] : '';
			if(!preg_match( '/^(day|month|year|week)s?$/', $unit)){
				$unit = 'days';
			}
			$date = gmdate('Y-m-d', strtotime(sprintf('today %1$s %2$s', $number, $unit)));
			return $date;
		}
	
		return false;
	}
	
	public function getDefaultOption($default = '', $args = ''){
		$args = wp_parse_args($args, array('multiple' => false));
		$options = (array) $this->getOption('default');
		$values = array();
	
		if(empty($options)){
			return $args['multiple'] ? $values : $default;
		}
	
		foreach($options as $opt){
			$opt = sanitize_key($opt);
			if('user_' == substr($opt, 0, 5) && is_user_logged_in()){
				$primaryProps = array('user_login', 'user_email', 'user_url');
				$opt = in_array($opt, $primaryProps) ? $opt : substr($opt, 5);
				$user = wp_get_current_user();
				$userProp = $user->get($opt);
				if(!empty($userProp)){
					if($args['multiple']){
						$values[] = $userProp;
					}else{
						return $userProp;
					}
				}
			}else if('post_meta' == $opt && in_the_loop()){
				if($args['multiple']){
					$values = array_merge( $values, PostMeta::find(get_the_ID(), $this->name));
				}else{
					$val = (string) PostMeta::find(get_the_ID(), $this->name, true);
					if(strlen($val)){
						return $val;
					}
				}
			}else if(('get' == $opt || 'post' == $opt) && !is_null($this->getParam($this->name, null))){
				$vals = (array) $this->getParam($this->name);
				$vals = array_map(function($text){
					return self::sanitizeQueryVar($text);
				}, $vals);
	
					if ($args['multiple']){
						$values = array_merge($values, $vals);
					}else{
						$val = isset($vals[0]) ? (string) $vals[0] : '';
						if(strlen($val)){
							return $val;
						}
					}
			}
		}
	
		if($args['multiple']){
			$values = array_unique($values);
			return $values;
		}else{
			return $default;
		}
	
	}
	
	public function getDataOption($args = ''){
		$options = (array) $this->getOption('data');
		if($this->filter === null){
			return null;
		}
		return $this->filter->apply( 'cppress-cf-tag-data-options', null, $options, $args);
	}
	
	
	
}