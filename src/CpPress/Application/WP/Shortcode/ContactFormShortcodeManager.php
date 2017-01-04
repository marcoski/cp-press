<?php
namespace CpPress\Application\WP\Shortcode;

use Commonhelp\Util\Shortcode;
use CpPress\Application\FrontEndApplication;
use Commonhelp\WP\WPContainer;

class ContactFormShortcodeManager extends Shortcode{
	
	public static $fields;
	
	private $exec = true;
	
	private $scannedTags = null;
	
	public static function init(){
		self::$fields['text'] = array('title' => __('Text Tag', 'cppress'), 'label' => __('Text', 'cppress'));
		self::$fields['email'] = array('title' => __('Email Tag', 'cppress'), 'label' => __('Email', 'cppress'));
		self::$fields['url'] = array('title' => __('URL Tag', 'cppress'), 'label' => __('URL', 'cppress'));
		self::$fields['phone'] = array('title' => __('Phone Tag', 'cppress'), 'label' => __('Phone', 'cppress'));
		self::$fields['number'] = array('title' => __('Number Tag', 'cppress'), 'label' => __('Number', 'cppress'));
		self::$fields['date'] = array('title' => __('Date Tag', 'cppress'), 'label' => __('Date', 'cppress'));
		self::$fields['textarea'] = array('title' => __('Text area Tag', 'cppress'), 'label' => __('Text area', 'cppress'));
		self::$fields['select'] = array('title' => array('Drop-down menu', 'cppress'), 'label' => __('Drop down menu', 'cppress'));
		self::$fields['checkbox'] = array('title' => array('Checkboxes Tag', 'cppress'), 'label' => __('Checkboxes', 'cppress'));
		self::$fields['radio'] = array('title' => array('Radio buttons Tag', 'cppress'), 'label' => __('Radio buttons', 'cppress'));
		self::$fields['acceptance'] = array('title' => __('Acceptance Tag', 'cppress'), 'label' => __('Acceptance', 'cppress'));
		self::$fields['file'] = array('title' => __('File', 'cppress'), 'label' => __('File', 'cppress'));
	}
	
	public function __construct(WPContainer $container){
		$this->container = $container;
		parent::__construct();
		$this->enctype = null;
	}
	
	public function register(){
		foreach(self::$fields as $k => $f){
			$this->addShortcode($k, function($tag) use($k){
				return FrontEndApplication::part('ContactForm', 'doShortcode', $this->container, array($tag));
			});
			$this->addShortcode($k . '*', function($tag) use($k){
				return FrontEndApplication::part('ContactForm', 'doShortcode', $this->container, array($tag));
			});
		}
	}
	
	public function getScannedTags(){
		return $this->scannedTags;
	}
	
	public function scanShortcode($content){
		$this->doShortcode($content, false);
		return $this->getScannedTags();
	}
	
	public function doShortcode($content, $exec=true){
		$this->exec = (bool) $exec;
		$this->scannedTags = array();
		if (empty($this->toArray())){
			return $content;
		}
		$pattern = $this->getRegex();
		return preg_replace_callback( '/' . $pattern . '/s', function($m){
			return $this->doShortCodeTag($m);
		}, $content );
	}
	
	public function getRegex(array $tagNames = array()){
		$tagNames = array_keys($this->toArray());
		$tagregexp = join('|', array_map( 'preg_quote', $tagNames));
		return '(\[?)'
				. '\[(' . $tagregexp . ')(?:[\r\n\t ](.*?))?(?:[\r\n\t ](\/))?\]'
				. '(?:([^[]*?)\[\/\2\])?'
				. '(\]?)';
	}
	
	protected function doShortCodeTag($m){
		// allow [[foo]] syntax for escaping a tag
		if($m[1] == '[' && $m[6] == ']'){
			return substr( $m[0], 1, -1 );
		}
		
		$tag = $m[2];
		$attr = $this->parseAtts($m[3]);
		$scannedTag = array(
			'type' => $tag,
			'baseType' => trim($tag, '*'),
			'name' => '',
			'options' => array(),
			'raw_values' => array(),
			'values' => array(),
			'pipes' => null,
			'labels' => array(),
			'attr' => '',
			'content' => ''
		);
		if(is_array($attr)){
			if (is_array($attr['options'])){
				if(!empty( $attr['options'] ) ) {
					$scannedTag['name'] = array_shift($attr['options']);
					if (!$this->isName($scannedTag['name'])){
						return $m[0]; // Invalid name is used. Ignore this tag.
					}
				}
				
				$scannedTag['options'] = (array) $attr['options'];
			}
			
			$scannedTag['raw_values'] = (array) $attr['values'];
			$scannedTag['values'] = $scannedTag['raw_values'];
			$scannedTag['labels'] = $scannedTag['values'];
			
		}else{
			$scannedTag['attr'] = $attr;
		}
		
		$scannedTag['values'] = array_map('trim', $scannedTag['values']);
		$scannedTag['labels'] = array_map('trim', $scannedTag['labels']);
		
		$content = trim($m[5]);
		$content = preg_replace("/<br[\r\n\t ]*\/?>$/m", '', $content);
		$scannedTag['content'] = $content;
		
		//$scannedTag = apply_filters( 'wpcf7_form_tag', $scannedTag, $this->exec );
		
		$this->scannedTags[] = $scannedTag;
		if ($this->exec){
			$func = $this[$tag];
			return $m[1] . call_user_func($func, $scannedTag) . $m[6];
		}else{
			return $m[0];
		}
	}
	
	public function parseAtts($text){
		$atts = array('options' => array(), 'values' => array());
		$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text );
		$text = stripcslashes(trim( $text ));
		$pattern = '%^([-+*=0-9a-zA-Z:.!?#$&@_/|\%\r\n\t ]*?)((?:[\r\n\t ]*"[^"]*"|[\r\n\t ]*\'[^\']*\')*)$%';
		if(preg_match($pattern, $text, $match)){
			if(!empty($match[1])){
				$atts['options'] = preg_split('/[\r\n\t ]+/', trim($match[1]));
			}
			if(!empty( $match[2])){
				preg_match_all('/"[^"]*"|\'[^\']*\'/', $match[2], $matched_values);
				$atts['values'] = self::stripQuoteDepp($matched_values[0]);
			}
		}else{
			$atts = $text;
		}
		
		return $atts;
	}
	
	public static function stripQuoteDepp($arr){
		if(is_string($arr)){
			return self::stripQuote($arr);
		}
		if(is_array($arr)){
			$result = array();
			foreach($arr as $key => $text){
				$result[$key] = self::stripQuoteDepp( $text );
			}
			return $result;
		}
	}
	
	public static function stripQuote($text){
		$text = trim( $text );
		
		if (preg_match('/^"(.*)"$/', $text, $matches)){
			$text = $matches[1];
		}else if(preg_match("/^'(.*)'$/", $text, $matches)){
			$text = $matches[1];
		}
		
		return $text;
	}
	
	public static function flatJoin($input){
		$input = self::flatten($input);
		$output = array();
		foreach((array) $input as $value){
			$output[] = trim((string) $value); 
		}
		
		return implode(', ', $output);
	}
	
	public static function flatten($input){
		if(!is_array($input)){
			return array($input);
		}
		
		$output = array();
		foreach($input as $value){
			$ouput = array_merge($output, self::flatten($value));
		}
		
		return $output;
	}
	
	public function isMultiPartForm(){
		foreach($this->scannedTags as $tag){
			if($tag['baseType'] == 'file'){
				return true;
			}
		}
		
		return false;
	}
	
	public function getEnctype(){
		return $this->enctype;
	}
	
	protected function isName($string){
		return preg_match('/^[A-Za-z][-A-Za-z0-9_:.]*$/', $string);
	}
	
}