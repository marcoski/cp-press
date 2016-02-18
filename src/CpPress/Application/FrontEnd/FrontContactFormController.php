<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Theme\Media\Image;
use Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Query\Query;
use Commonhelp\WP\WPTemplate;
use Commonhelp\Util\Inflector;
use CpPress\Application\WP\Shortcode\ContactFormShortcode;
use CpPress\Application\WP\Admin\PostMeta;

class FrontContactFormController extends WPController{
	
	private $filter;
	private $cfShortcode;
	
	private $type;
	private $baseType;
	private $name = '';
	private $options = array();
	private $rawValues = array();
	private $values = array();
	private $labels = array();
	private $attr = '';
	private $content = '';
	
	private $unitTag;
	
	private $title;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter, ContactFormShortcode $cfShortcode){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
		$this->cfShortcode = $cfShortcode;
	}
	
	
	public function form_template($instance){
		$this->title = $instance['wtitle'];
		$form = $instance['form'];
		$content = $this->cfShortcode->doShortcode($form);
		$this->unitTag = self::getUnitTag($instance['widget']['section'] + $instance['widget']['grid'] + $instance['widget']['cell'] + $instance['widget']['id']);
		$divAtts = array(
			'role' => 'form',
			'class' => 'cppress-cf',
			'id' => $this->unitTag,
		);
		
		$url = '';
		if($instance['desturi'] != '' && $instance['desturi'] != get_permalink()){
			$url = $instance['desturi'];
		}
		$url .= '#' . $this->unitTag;
		$url = $this->filter->apply('cppress-cf-form-action-url', $url);
		
		$idAttr = $this->filter->apply('cppress-cf-form-id-attr', '', $this->title);
		$nameAttr = $this->filter->apply('cppress-cf-form-name-attr', '', $this->title);
		$classes = $this->filter->apply('cppress-cf-form-classes', array('cppress-cf-form'), $this->title);
		$classes = array_map('sanitize_html_class', $classes);
		$classes = array_filter($classes);
		$classes = array_unique($classes);
		$class = implode( ' ', $classes);
		$enctype = $this->filter->apply('cppress-cf-form-enctype', '', $this->title);
		$formAtts = array(
			'action' => esc_url($url),
			'method' => 'post',
			'class' => $class,
			'enctype' => $this->enctypeValue($enctype),
			'novalidate' => $novalidate ? 'novalidate' : '' 
		);
		$this->hiddenFields();
		$this->assign('formAtts', $this->formatAtts($formAtts));
		$this->assign('divAtts', $this->formatAtts($divAtts));
		$this->assign('content', $content);
		$this->assign('instance', $instance);
		$this->assign('filter', $this->filter);
		$this->assign('title', $this->title);
	}
	
	/**
	 * @responder string
	 */
	public function doShortcode($tag){
		foreach($tag as $key => $value){
			if(property_exists( __CLASS__, $key )){
				$this->{$key} = $value;
			}
		}
		
		if(empty($this->name)){
			return '';
		}
		
		$validationError = ''; /** TODO VALIDATION */
		$class = $this->getFormClass($this->type);
		if($validationError){
			$class .= 'cppress-cf-not-valid';
		}
		
		$atts = array();
		$atts['class'] = $this->getClassOptions($class);
		$atts['id'] = $this->getOption('id', 'id', true);
		$atts['tabindex'] = $this->getOption('tabindex', 'int', true);
		
		if($this->isRequired()){
			$atts['aria-required'] = true;
		}
		$atts['aria-invalid'] = $validationError ? 'true' : 'false';
		
		$handler = 'do' . ucfirst($this->baseType) . 'Shortcode';
		
		if(method_exists($this, $handler)){
			return $this->$handler($atts, $validationError);
		}
		
		return '';
	}
	

	public function doTextShortcode($atts, $validationError){
		$atts['size'] = $this->getSizeOption('40');
		$atts['maxlength'] = $this->getMaxLengthOption();
		$atts['minlength'] = $this->getMinLengthOption();
		if ($atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength']){
			unset( $atts['maxlength'], $atts['minlength'] );
		}
		
		if($this->hasOption('readonly')){
			$atts['readonly'] = 'readonly';
		}
		
		$value = (string) reset($this->values);
		
		if($this->hasOption('placeholder') || $this->hasOption('watermark')){
			$atts['placeholder'] = $value;
			$value = '';
		}
		
		$value = $this->getDefaultOption($value);
		$atts['value'] = $value;
		
		$atts['type'] = 'text';
		$atts['name'] = $this->name;
		
		$atts = $this->formatAtts($atts);
		return sprintf(
			$this->filter->apply(
				'cppress-cf-text',
				'<span class="cppress-cf-form-control-wrap %1$s"><input %2$s />%3$s</span>',
				$this->name,
				$this->title
			),
			sanitize_html_class($this->name ), $atts, $validationError);
	}
	

	public function doEmailShortcode($atts, $validationError){
		return $this->doTextShortcode($atts, $validationError);
	}
	
	public function doUrlShortcode($atts, $validationError){
		return $this->doTextShortcode($atts, $validationError);
	}
	
	public function doPhoneShortcode($atts, $validationError){
		return $this->doTextShortcode($atts, $validationError);
	}
	
	public function doNumberShortcode($atts, $validationError){
		$atts['class'] .= ' cppress-validates-as-number';
		$atts['min'] = $this->getOption('number-min', 'signed_int', true);
		$atts['max'] = $this->getOption('number-max', 'signed_int', true);
		$atts['step'] = $this->getOption('number-step', 'int', true);
		
		if($this->hasOption('readonly')){
			$atts['readonly'] = 'readonly';
		}
		
		$value = (string) reset($this->values);
		if($this->hasOption('placeholder') || $this->hasOption('watermark')){
			$atts['placeholder'] = $value;
			$value = '';
		}
		
		$value = $this->getDefaultOption($value);
		$atts['value'] = $value;
		$atts['type'] = 'number';
		$atts['name'] = $this->name;
		$atts = $this->formatAtts($atts);
		return sprintf(
			$this->filter->apply(
					'cppress-cf-number',
					'<span class="cppress-cf-form-control-wrap %1$s"><input %2$s />%3$s</span>',
					$this->name,
					$this->title
			),
			sanitize_html_class($this->name ), $atts, $validationError);
	}

	public function doDateShortcode($atts, $validationError){
		$atts['class'] .= ' cppress-validates-as-date';
		$atts['min'] = $this->getDateOption('date-min');
		$atts['max'] = $this->getDateOption('date-max');
		$atts['step'] = $this->getOption('date-step', 'int', true);
		
		if($this->hasOption('readonly')){
			$atts['readonly'] = 'readonly';
		}
		
		$value = (string) reset($this->values);
		if($this->hasOption('placeholder') || $this->hasOption('watermark')){
			$atts['placeholder'] = $value;
			$value = '';
		}
		
		$value = $this->getDefaultOption($value);
		$atts['value'] = $value;
		$atts['type'] = 'date';
		$atts['name'] = $this->name;
		$atts = $this->formatAtts($atts);
		return sprintf(
			$this->filter->apply(
					'cppress-cf-date',
					'<span class="cppress-cf-form-control-wrap %1$s"><input %2$s />%3$s</span>',
					$this->name,
					$this->title
			),
			sanitize_html_class($this->name ), $atts, $validationError);
	}
	
	public function doTextareaShortcode($atts, $validationError){
		$atts['cols'] = $this->getColsOption('40');
		$atts['rows'] = $this->getRowsOption('10');
		$atts['maxlength'] = $this->getMaxLengthOption();
		$atts['minlength'] = $this->getMinLengthOption();
		
		if($atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength']){
			unset($atts['maxlength'], $atts['minlength']);
		}
		
		if($this->hasOption('readonly')){
			$atts['readonly'] = 'readonly';
		}
		
		if($this->hasOption('placeholder') || $this->hasOption('watermark')){
			$atts['placeholder'] = $value;
			$value = '';
		}
		
		$value = empty($this->content) ? (string) reset($this->values) : $this->content;
		
		$value = $this->getDefaultOption($value);
		$atts['name'] = $this->name;
		$atts = $this->formatAtts($atts);
		return sprintf(
				$this->filter->apply(
						'cppress-cf-textarea',
						'<span class="cppress-cf-form-control-wrap %1$s"><textarea %2$s>%3$s</textarea>%4$s</span>',
						$this->name,
						$this->title
				),
				sanitize_html_class($this->name), $atts,
				esc_textarea($value), $validationError);
		
	}
	
	public function doSelectShortcode($atts, $validationError){
		$multiple = $this->hasOption('multiple');
		$includeBlank = $this->hasOption('blankfirst');
		$firstAsLabel = $this->hasOption('firstaslabel');
		$values = $this->values;
		$labels = $this->labels;
		if($data = (array) $this->getDataOption()){
			$values = array_merge( $values, array_values( $data ) );
			$labels = array_merge( $labels, array_values( $data ) );
		}
		
		$defaults = array();
		$defaultChoice = $this->getDefaultOption(null, 'multiple=1');
		foreach($defaultChoice as $value){
			$key = array_search($value, $values, true);
			if(false !== $key){
				$defaults[] = (int) $key + 1;
			}
		}
		if($matches = $this->getFirstMatchOption( '/^default:([0-9_]+)$/')){
			$defaults = array_merge($defaults, explode('_', $matches[1]));
		}
		$defaults = array_unique($defaults);
		$shifted = false;
		if($includeBlank || empty($values)){
			array_unshift($labels, '---');
			array_unshift($values, '');
			$shifted = true;
		}else if($firstAsLabel){
			$values[0] = '';
		}
		
		$html = '';
		foreach($values as $key => $value){
			$selected = false;
			if (!$shifted && in_array((int) $key + 1, (array) $defaults)){
				$selected = true;
			}else if($shifted && in_array((int) $key, (array) $defaults)){
				$selected = true;
			}
			
			$itemAtts = array(
					'value' => $value,
					'selected' => $selected ? 'selected' : '');
			$itemAtts = $this->formatAtts($itemAtts);
			$label = isset($labels[$key]) ? $labels[$key] : $value;
			$html .= sprintf('<option %1$s>%2$s</option>', $itemAtts, esc_html($label));
		}
		
		if($multiple){
			$atts['multiple'] = 'multiple';
		}
		
		$atts['name'] = $this->name . ( $multiple ? '[]' : '' );
		$atts = $this->formatAtts($atts);
		return sprintf(
				$this->filter->apply(
						'cppress-cf-select',
						'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
						$this->name,
						$this->title
				),
				sanitize_html_class($this->name), $atts, $html, $validationError);
	}
	
	public function doCheckboxShortcode($atts, $validationError){
		$labelFirst = $this->hasOption('labelfirst');
		$useLabelElement = $this->hasOption('uselabelelement');
		$exclusive = $this->hasOption('exclusive:on');
		$multiple = false;
		
		if('checkbox' == $this->baseType){
			$multiple = !$exclusive;
		}else{ // radio
			$exclusive = false;
		}
		
		if($exclusive){
			$atts['class'] .= ' cppress-cf-exclusive-checkbox';
		}
		$tabIndex = $atts['tabindex'];
		unset($atts['tabindex']);
		
		if(false !== $tabindex){
			$tabindex = absint($tabindex);
		}
		
		$html = '';
		$count = 0;
		$values = (array) $this->values;
		$labels = (array) $this->labels;
		
		if ($data = (array) $this->getDataOption()) {
			if($freeText){
				$values = array_merge(
						array_slice($values, 0, -1),
						array_values($data),
						array_slice($values, -1 ));
				$labels = array_merge(
						array_slice($labels, 0, -1),
						array_values($data),
						array_slice($labels, -1));
			}else{
				$values = array_merge($values, array_values($data));
				$labels = array_merge($labels, array_values($data));
			}
		}
		$defaults = array();
		$defaultChoice = $this->getDefaultOption(null, 'multiple=1');
		foreach($defaultChoice as $value){
			$key = array_search($value, $values, true);
			if(false !== $key){
				$defaults[] = (int) $key + 1;
			}
		}
		
		if ($matches = $this->getFirstMatchOption('/^default:([0-9_]+)$/')) {
			$defaults = array_merge($defaults, explode('_', $matches[1]));
		}
		$defaults = array_unique($defaults);
		
		foreach($values as $key => $value){
			$class = 'cppress-cf-list-item';
			$checked = false;
			$checked = in_array($key + 1, (array) $defaults);
			
			if(isset($labels[$key])){
				$label = $labels[$key];
			}else{
				$label = $value;
			}
			
			$itemAtts = array(
				'type' => $this->baseType,
				'name' => $this->name . ( $multiple ? '[]' : '' ),
				'value' => $value,
				'checked' => $checked ? 'checked' : '',
				'tabindex' => $tabindex ? $tabindex : '' 
			);
			$itemAtts = $this->formatAtts($itemAtts);
			
			if($labelFirst){ // put label first, input last
				$item = sprintf(
						$this->filter->apply(
							'cppress-cf-list-item-label-first',
							'<span class="cppress-cf-list-item-label">%1$s</span>&nbsp;<input %2$s />',
							$this->name,
							$this->title
						),
						esc_html($label), $itemAtts);
			}else{
				$item = sprintf(
						$this->filter->apply(
							'cppress-cf-list-item-label-last',
							'<input %2$s />&nbsp;<span class="cppress-cf-list-item-label">%1$s</span>',
							$this->name, 
							$this->title
						),
						esc_html($label), $itemAtts);
			}
			if($useLabelElement){
				$item = '<label>' . $item . '</label>';
			}
			
			if(false !== $tabindex){
				$tabindex += 1;
			}
			
			$count += 1;
			if(1 == $count){
				$class .= ' first';
			}
			
			if(count( $values ) == $count){ // last round
				$class .= ' last';
			}
			$item = '<span class="' . esc_attr($class) . '">' . $item . '</span>';
			$html .= $item;
		}
		
		$atts = $this->formatAtts($atts);
		return sprintf(
				$this->filter->apply(
						'cppress-cf-checkbox',
						'<span class="cppress-cf-form-control-wrap %1$s"><span %2$s>%3$s</span>%4$s</span>',
						$this->name,
						$this->title
				),
				sanitize_html_class($this->name), $atts, $html, $validationError);
		
	}
	
	public function doRadioShortcode($atts, $validationError){
		return $this->doCheckboxShortcode($atts, $validationError);
	}
	
	public function doAcceptanceShortcode($atts, $validationError){
		if($this->hasOption('default:on')){
			$atts['checked'] = 'checked';
		}
		$atts['type'] = 'checkbox';
		$atts['name'] = $this->name;
		$atts['value'] = '1';
		
		$atts = $this->formatAtts($atts);
		return sprintf(
				$this->filter->apply(
						'cppress-cf-acceptance',
						'<span class="cppress-cf-form-control-wrap %1$s"><input %2$s />%3$s</span>',
						$this->name,
						$this->title
				),
				sanitize_html_class($this->name), $atts, $validationError);
		
	}
	
	public function doFileShortcode($atts, $validationError){
		$atts['size'] = $this->getSizeOption('40');
		$atts['type'] = 'file';
		$atts['name'] = $this->name;
		
		$atts = $this->formatAtts($atts);
		return sprintf(
			$this->filter->apply(
					'cppress-cf-file',
					'<span class="cppress-cf-form-control-wrap %1$s"><input %2$s />%3$s</span>',
					$this->name,
					$this->title
			),
			sanitize_html_class($this->name), $atts, $validationError);
	}
	
	
	public function isRequired(){
		return ('*' == substr( $this->type, -1 ));
	}
	
	public function hasOption($opt){
		$pattern = sprintf('/^%s(:.+)?$/i', preg_quote($opt, '/'));
		return (bool) preg_grep($pattern, $this->options);
	}
	
	
	
	private function getFormClass($type, $default = ''){
		$type = trim($type);
		$default = array_filter(explode(' ', $default));
		$classes = array_merge( array('cppress-cf-form-control'), $default);
		$typebase = rtrim($type, '*');
		$required = ('*' == substr( $type, -1 ));
		$classes[] = 'cppress-cf-' . $typebase;
		if ($required){
			$classes[] = 'cpprss-cf-validates-as-required';
		}
		$classes = $this->filter->apply('cppress-cf-form-classes', array_unique($classes), $this->title);
		return implode(' ', $classes);
	}
	
	private function getClassOptions($default=''){
		if(is_string($default)){
			$default = explode(' ', $default);
		}
		$options = array_merge(
				(array) $default,
				(array) $this->getOption('class', 'class'));
		$options = array_filter(array_unique($options));
		return implode(' ', $options);
	}
	
	private function getOption($opt, $pattern = '', $single = false){
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
	
	private function getDefaultOption($default = '', $args = ''){
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
					return $this->sanitizeQueryVar($text);
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
	
	private function getSizeOption($default=''){
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
	
	private function getMaxLengthOption($default=''){
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
	
	private function getMinLengthOption($default=''){
		$option = $this->getOption( 'minlength', 'int', true );
		if($option){
			return $option;
		}
		
		return $default;
	}
	
	private function getRowsOption($default=''){
		$matchesAll = $this->getAllMatchOptions('%^([0-9]*)x([0-9]*)(?:/[0-9]+)?$%');
		foreach((array) $matchesAll as $matches){
			if (isset( $matches[2] ) && '' !== $matches[2]){
				return $matches[2];
			}
		}
		
		return $default;
	}
	
	private function getColsOption($default=''){
		$matchesAll = $this->getAllMatchOptions('%^([0-9]*)x([0-9]*)(?:/[0-9]+)?$%');
		foreach((array) $matchesAll as $matches){
			if(isset( $matches[1] ) && '' !== $matches[1]){
				return $matches[1];
			}
		}
		
		return $default;
	}
	
	private function getDateOption($opt){
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
	
	private function getDataOption($args = ''){
		$options = (array) $this->getOption('data');
		return $this->filter->apply( 'cppress-cf-tag-data-options', null, $options, $args);
	}
	
	private function getFirstMatchOption($pattern){
		foreach((array) $this->options as $option){
			if(preg_match($pattern, $option, $matches)){
				return $matches;
			}
		}
		
		return false;
	}
	
	private function getAllMatchOptions($pattern){
		$result = array();
		foreach((array) $this->options as $option){
			if(preg_match($pattern, $option, $matches)){
				$result[] = $matches;
			}
		}
		
		return $result;
	}
	
	private function sanitizeQueryVar($text){
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
	
	private function formatAtts($atts){
		$html = '';
		$prioritizedAtts = array( 'type', 'name', 'value' );
		foreach($prioritizedAtts as $att){
			if(isset($atts[$att])){
				$value = trim($atts[$att]);
				$html .= sprintf(' %s="%s"', $att, esc_attr($value));
				unset($atts[$att]);
			}
		}
		
		foreach($atts as $key => $value){
			$key = strtolower(trim($key));
			if (!preg_match('/^[a-z_:][a-z_:.0-9-]*$/', $key)){
				continue;
			}
			$value = trim($value);
			if('' !== $value){
				$html .= sprintf(' %s="%s"', $key, esc_attr($value));
			}
		}
		
		$html = trim($html);
		return $html;
	}
	
	private function enctypeValue($enctype){
		$enctype = trim($enctype);
		if(empty($enctype)){
			return '';
		}
		
		$validEnctypes = array(
			'application/x-www-form-urlencoded',
			'multipart/form-data',
			'text/plain' 
		);
		if(in_array( $enctype, $validEnctypes)){
			return $enctype;
		}
		
		$pattern = '%^enctype="(' . implode('|', $valid_enctypes) . ')"$%';
		if(preg_match($pattern, $enctype, $matches)){
			return $matches[1]; // for back-compat
		}
		return '';
	}
	
	private function hiddenFields(){
		$hiddenFields = array(
			'_cppress-cf-unit-tag' => $this->unitTag,
			'_wpnonce' => wp_create_nonce('cppress-cf')
		);
		$this->assign('hiddenFields', $hiddenFields);
	}
	
	private static function getUnitTag($id = 0){
		static $globalCount = 0;
		
		$globalCount += 1;
		
		if(in_the_loop()){
			$unitTag = sprintf('cppress-cf-f%1$d-p%2$d-o%3$d',
					absint($id), get_the_ID(), $globalCount);
		} else {
			$unitTag = sprintf('cppress-cf-f%1$d-o%2$d',
					absint($id), $global_count);
		}
		return $unitTag;
	}
	
	private function assignTemplate($instance, $tPreName){
		$template = new WPTemplate($this);
		$template->setTemplateDirs(array(get_template_directory().'/', get_stylesheet_directory().'/'));
		if($instance['wtitle'] !== ''){
			$templateName = $this->filter->apply('cppress_widget_post_template_name',
					'template-parts/' . $tPreName . '-' .
					Inflector::delimit(Inflector::camelize($instance['wtitle']), '-'), $instance);
		}else{
			$templateName = $this->filter->apply('cppress_widget_post_template_name',
					'template-parts/' . $tPreName, $instance);
		}
		$this->assign('templateName', $templateName);
		$this->assign('template', $template);
	}
	
	
	
}