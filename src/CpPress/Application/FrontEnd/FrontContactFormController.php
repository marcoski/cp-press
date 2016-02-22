<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Theme\Media\Image;
use Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Query\Query;
use Commonhelp\WP\WPTemplate;
use Commonhelp\Util\Inflector;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Submitter\ContactFormSubmitter;
use CpPress\Application\WP\Shortcode\ContactFormShortcodeManager;
use CpPress\Application\WP\Shortcode\ContactFormShortcode;

class FrontContactFormController extends WPController{
	
	private $filter;
	private $cfShortcode;
	private $unitTag;
	private $title;
	private $tag;
	
	private $responseCount = 0;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter, ContactFormShortcodeManager $cfShortcode){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
		$this->cfShortcode = $cfShortcode;
	}
	
	public function submit(ContactFormSubmitter $submitter, $winstance, $wargs){
		if(!isset($_SERVER['REQUEST_METHOD'])){
			return;
		}
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !is_null($this->getParam('_cppress-cf-unit-tag', null))){
			if(!is_null($this->getParam('_cppress-cf-isajaxcall'))){
				$result = $submitter->ajaxSubmit($winstance, $wargs);
				return $this->filter->apply('cppress-cf-ajax-json', $result);
			}
			
			$submitter->nonajaxSubmit($winstance, $wargs);
			$this->form_template($instance, true, $submitter);
			return new WPTemplateResponse($this, 'form_template');
		}
	}
	
	
	public function form_template($instance, $isPosted=false, $submitter=null){
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
			$url .= '#' . $this->unitTag;
			$url = $this->filter->apply('cppress-cf-form-action-url', $url);
		}else{
			$url = admin_url('admin-ajax.php');	
		}
		
		$idAttr = $this->filter->apply('cppress-cf-form-id-attr', '', $this->title);
		$nameAttr = $this->filter->apply('cppress-cf-form-name-attr', '', $this->title);
		$classes = $this->filter->apply('cppress-cf-form-classes', array('cppress-cf-form'), $this->title);
		$classes = array_map('sanitize_html_class', $classes);
		$classes = array_filter($classes);
		$classes = array_unique($classes);
		$class = implode( ' ', $classes);
		if($this->cfShortcode->isMultiPartForm()){
			$enctype = 'multipart/form-data';
		}else{
			$enctype = null;
		}
		
		$formAtts = array(
			'action' => esc_url($url),
			'method' => 'post',
			'class' => $class,
			'enctype' => $this->enctypeValue($enctype),
			'novalidate' => $novalidate ? 'novalidate' : '' 
		);
		$this->hiddenFields();
		if($isPosted){
			$this->assign('screenReaderContent', $this->screenReaderResponse($submitter));
			$this->assign('responseOutput', $this->responseOuput($submitter));
			$screenReaderAtts = array(
				'class' => 'screen-reader-response',
				'role' => 'alert'
			);
		}else{
			$this->assign('screenReaderContent', '');
			$this->assign('responseOutput', $this->responseOuput(null));
			$screenReaderAtts = array(
					'class' => 'screen-reader-response',
			);
		}
		$this->assign('screenReaderAtts', $this->formatAtts($screenReaderAtts));
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
		$this->tag = new ContactFormShortcode($tag, $this->request, $this->filter);
		if($this->tag->name == ''){
			return '';
		}
		$validationError = ''; /** TODO VALIDATION */
		$class = $this->getFormClass($this->tag->type);
		if($validationError){
			$class .= 'cppress-cf-not-valid';
		}
		
		$atts = array();
		$atts['class'] = $this->tag->getClassOptions($class);
		$atts['id'] = $this->tag->getOption('id', 'id', true);
		$atts['tabindex'] = $this->tag->getOption('tabindex', 'int', true);
		
		if($this->tag->isRequired()){
			$atts['aria-required'] = true;
		}
		$atts['aria-invalid'] = $validationError ? 'true' : 'false';
		
		$handler = 'do' . ucfirst($this->tag->baseType) . 'Shortcode';
		if(method_exists($this, $handler)){
			return $this->$handler($atts, $validationError);
		}
		
		return '';
	}
	

	public function doTextShortcode($atts, $validationError){
		$atts['size'] = $this->tag->getSizeOption('40');
		$atts['maxlength'] = $this->tag->getMaxLengthOption();
		$atts['minlength'] = $this->tag->getMinLengthOption();
		if ($atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength']){
			unset( $atts['maxlength'], $atts['minlength'] );
		}
		
		if($this->tag->hasOption('readonly')){
			$atts['readonly'] = 'readonly';
		}
		
		$value = (string) implode(' ', $this->tag->values);
		
		if($this->tag->hasOption('placeholder') || $this->tag->hasOption('watermark')){
			$atts['placeholder'] = $value;
			$value = '';
		}
		
		$value = $this->tag->getDefaultOption($value);
		$atts['value'] = $value;
		
		$atts['type'] = 'text';
		$atts['name'] = $this->tag->name;
		
		$atts = $this->formatAtts($atts);
		return sprintf(
			$this->filter->apply(
				'cppress-cf-text',
				'<span class="cppress-cf-control-wrap %1$s"><input %2$s />%3$s</span>',
				$this->tag->name,
				$this->title,
					$this->tag
			),
			sanitize_html_class($this->tag->name ), $atts, $validationError);
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
		$atts['min'] = $this->tag->getOption('number-min', 'signed_int', true);
		$atts['max'] = $this->tag->getOption('number-max', 'signed_int', true);
		$atts['step'] = $this->tag->getOption('number-step', 'int', true);
		
		if($this->tag->hasOption('readonly')){
			$atts['readonly'] = 'readonly';
		}
		
		$value = (string) implode(' ', $this->tag->values);
		if($this->tag->hasOption('placeholder') || $this->tag->hasOption('watermark')){
			$atts['placeholder'] = $value;
			$value = '';
		}
		
		$value = $this->tag->getDefaultOption($value);
		$atts['value'] = $value;
		$atts['type'] = 'number';
		$atts['name'] = $this->name;
		$atts = $this->formatAtts($atts);
		return sprintf(
			$this->filter->apply(
					'cppress-cf-number',
					'<span class="cppress-cf-control-wrap %1$s"><input %2$s />%3$s</span>',
					$this->tag->name,
					$this->title,
					$this->tag
			),
			sanitize_html_class($this->tag->name ), $atts, $validationError);
	}

	public function doDateShortcode($atts, $validationError){
		$atts['class'] .= ' cppress-validates-as-date';
		$atts['min'] = $this->tag->getDateOption('date-min');
		$atts['max'] = $this->tag->getDateOption('date-max');
		$atts['step'] = $this->tag->getOption('date-step', 'int', true);
		
		if($this->tag->hasOption('readonly')){
			$atts['readonly'] = 'readonly';
		}
		
		$value = (string) reset();
		if($this->tag->hasOption('placeholder') || $this->tag->hasOption('watermark')){
			$atts['placeholder'] = $value;
			$value = '';
		}
		
		$value = $this->tag->getDefaultOption($value);
		$atts['value'] = $value;
		$atts['type'] = 'date';
		$atts['name'] = $this->tag->name;
		$atts = $this->formatAtts($atts);
		return sprintf(
			$this->filter->apply(
					'cppress-cf-date',
					'<span class="cppress-cf-control-wrap %1$s"><input %2$s />%3$s</span>',
					$this->tag->name,
					$this->title,
					$this->tag
			),
			sanitize_html_class($this->tag->name ), $atts, $validationError);
	}
	
	public function doTextareaShortcode($atts, $validationError){
		$atts['cols'] = $this->tag->getColsOption('40');
		$atts['rows'] = $this->tag->getRowsOption('10');
		$atts['maxlength'] = $this->tag->getMaxLengthOption();
		$atts['minlength'] = $this->tag->getMinLengthOption();
		
		if($atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength']){
			unset($atts['maxlength'], $atts['minlength']);
		}
		
		if($this->tag->hasOption('readonly')){
			$atts['readonly'] = 'readonly';
		}
		
		if($this->tag->hasOption('placeholder') || $this->tag->hasOption('watermark')){
			$atts['placeholder'] = $value;
			$value = '';
		}
		
		$value = empty($this->tag->content) ? (string) implode(' ', $this->tag->values) : $this->tag->content;
		
		$value = $this->tag->getDefaultOption($value);
		$atts['name'] = $this->tag->name;
		$atts = $this->formatAtts($atts);
		return sprintf(
				$this->filter->apply(
						'cppress-cf-textarea',
						'<span class="cppress-cf-control-wrap %1$s"><textarea %2$s>%3$s</textarea>%4$s</span>',
						$this->tag->name,
						$this->title,
						$this->tag
				),
				sanitize_html_class($this->tag->name), $atts,
				esc_textarea($value), $validationError);
		
	}
	
	public function doSelectShortcode($atts, $validationError){
		$multiple = $this->tag->hasOption('multiple');
		$includeBlank = $this->tag->hasOption('blankfirst');
		$firstAsLabel = $this->tag->hasOption('firstaslabel');
		$values = $this->tag->values;
		$labels = $this->tag->labels;
		if($data = (array) $this->tag->getDataOption()){
			$values = array_merge( $values, array_values( $data ) );
			$labels = array_merge( $labels, array_values( $data ) );
		}
		
		$defaults = array();
		$defaultChoice = $this->tag->getDefaultOption(null, 'multiple=1');
		foreach($defaultChoice as $value){
			$key = array_search($value, $values, true);
			if(false !== $key){
				$defaults[] = (int) $key + 1;
			}
		}
		if($matches = $this->tag->getFirstMatchOption( '/^default:([0-9_]+)$/')){
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
		
		$atts['name'] = $this->tag->name . ( $multiple ? '[]' : '' );
		$atts = $this->formatAtts($atts);
		return sprintf(
				$this->filter->apply(
						'cppress-cf-select',
						'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
						$this->tag->name,
						$this->title,
						$this->tag
				),
				sanitize_html_class($this->tag->name), $atts, $html, $validationError);
	}
	
	public function doCheckboxShortcode($atts, $validationError){
		$labelFirst = $this->tag->hasOption('labelfirst');
		$useLabelElement = $this->tag->hasOption('uselabelelement');
		$exclusive = $this->tag->hasOption('exclusive:on');
		$multiple = false;
		
		if('checkbox' == $this->tag->baseType){
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
		$values = (array) $this->tag->values;
		$labels = (array) $this->tag->labels;
		
		if ($data = (array) $this->tag->getDataOption()) {
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
		$defaultChoice = $this->tag->getDefaultOption(null, 'multiple=1');
		foreach($defaultChoice as $value){
			$key = array_search($value, $values, true);
			if(false !== $key){
				$defaults[] = (int) $key + 1;
			}
		}
		
		if ($matches = $this->tag->getFirstMatchOption('/^default:([0-9_]+)$/')) {
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
				'type' => $this->tag->baseType,
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
							$this->tag->name,
							$this->title,
							$this->tag
						),
						esc_html($label), $itemAtts);
			}else{
				$item = sprintf(
						$this->filter->apply(
							'cppress-cf-list-item-label-last',
							'<input %2$s />&nbsp;<span class="cppress-cf-list-item-label">%1$s</span>',
							$this->tag->name, 
							$this->title,
							$this->tag
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
						'<span class="cppress-cf-control-wrap %1$s"><span %2$s>%3$s</span>%4$s</span>',
						$this->tag->name,
						$this->title,
						$this->tag
				),
				sanitize_html_class($this->tag->name), $atts, $html, $validationError);
		
	}
	
	public function doRadioShortcode($atts, $validationError){
		return $this->doCheckboxShortcode($atts, $validationError);
	}
	
	public function doAcceptanceShortcode($atts, $validationError){
		if($this->tag->hasOption('default:on')){
			$atts['checked'] = 'checked';
		}
		$atts['type'] = 'checkbox';
		$atts['name'] = $this->tag->name;
		$atts['value'] = '1';
		
		$atts = $this->formatAtts($atts);
		return sprintf(
				$this->filter->apply(
						'cppress-cf-acceptance',
						'<span class="cppress-cf-control-wrap %1$s"><input %2$s />%3$s</span>',
						$this->tag->name,
						$this->title,
						$this->tag
				),
				sanitize_html_class($this->tag->name), $atts, $validationError);
		
	}
	
	public function doFileShortcode($atts, $validationError){
		$atts['size'] = $this->tag->getSizeOption('40');
		$atts['type'] = 'file';
		$atts['name'] = $this->tag->name;
		
		$atts = $this->formatAtts($atts);
		return sprintf(
			$this->filter->apply(
					'cppress-cf-file',
					'<span class="cppress-cf-control-wrap %1$s"><input %2$s />%3$s</span>',
					$this->tag->name,
					$this->title,
					$this->tag
			),
			sanitize_html_class($this->tag->name), $atts, $validationError);
	}
	
	
	
	private function getFormClass($type, $default = ''){
		$type = trim($type);
		$default = array_filter(explode(' ', $default));
		$classes = array_merge( array('cppress-cf-form-control'), $default);
		$typebase = rtrim($type, '*');
		$required = ('*' == substr( $type, -1 ));
		$classes[] = 'cppress-cf-' . $typebase;
		if ($required){
			$classes[] = 'cppress-cf-validates-as-required';
		}
		$classes = $this->filter->apply('cppress-cf-form-classes', array_unique($classes), $this->title);
		return implode(' ', $classes);
	}
	
	
	public function formatAtts($atts){
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
	
	public function enctypeValue($enctype){
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
	
	public function hiddenFields(){
		$tags = htmlspecialchars(json_encode($this->cfShortcode->getScannedTags(), JSON_HEX_TAG));
		$hiddenFields = array(
			'_cppress-cf-unit-tag' => $this->unitTag,
			'_wpnonce' => wp_create_nonce('cppress-cf'),
			'_cppress-cf-scannedtag' => $tags,
			'_cppress-cf-id' => get_the_ID()
		);
		$this->assign('hiddenFields', $hiddenFields);
	}
	
	public static function getUnitTag($id = 0){
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
	
	private function screenReaderResponse(ContactFormSubmitter $submitter){
		$content = '';
		if($response = $submitter->getResponse()){
			$content = esc_html($response);
		}
		
		if($invalidFields = $submitter->getUploadedFiles()){
			$content .= "\n" . '<ul>' . "\n";
			foreach((array) $invalidFields as $name => $field){
				if($field['idref']){
					$link = sprintf('<a href="#%1s">%2$s</a>',
						esc_attr($field['idref']),
						esc_attr($field['reason'])	
					);
					$content .= sprintf('<li>%s</li>', $link);
				}else{
					$content .= sprintf('<li>%</li>', esc_html($field['reason']));
				}
				$content .= "\n";
			}
			
			$content .= "</ul>\n";
		}
		
		return $this->filter->apply('cppress-cf-screenreadercontent', $content, $submitter, $this->title);
	}
	
	private function responseOuput(ContactFormSubmitter $submitter = null){
		$class = 'cppress-cf-response-output';
		$role = '';
		$content = '';
		
		if(!is_null($submitter)){
			$role = 'alert';
			$content = $submitter->getResponse();
			if($submitter->is('validation_failed')){
				$class .= ' cppress-cf-validation-errors';
			}else if($submitter->is('spam')){
				$class .= ' cppress-cf-spam-blocked';
			}else if($submitter->is('mail_sent')){
				$class .= ' cppress-cf-mail-sent-ok';
			}else if($submitter->is('mail_failed')){
				$class .= 'cppress-cf-mail-sent-ng';
			}
		}else{
			$class .= ' cppress-cf-display-none';
		}
		
		$atts = array(
			'class' => trim($class),
			'role' => trim($role)
		);
		$atts = $this->formatAtts($atts);
		$output = sprintf('<div %1$s>%2$s</div>', $atts, esc_html($content));
		$output = $this->filter->apply('cppress-cf-response-output', $output, $content, $submitter, $this->title);
		$this->responseCount += 1;
		
		return $output;
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