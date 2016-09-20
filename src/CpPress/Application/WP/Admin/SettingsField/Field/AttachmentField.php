<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

class AttachmentField extends BaseField{
	
	public function render(array $args){
		$optVal = $this->getFieldValue();
		$element = '';
		if($args['tag'] == 'select' || $args['tag'] == 'selectmultiple'){
			$soptions = '';
			foreach($args['options'] as $key => $value){
				if(!is_array($value)){
					$soptions .= sprintf($this->tags['selectoption'],
							$key, selected($optVal, $key, false), $value);
				}else{
					$soptions .= sprintf($this->tags['optiongroupstart'], $key);
					foreach($value as $k => $v){
						$optVal = is_array($optVal) ? $optVal : array($optVal);
						$selected = in_array($k, $optVal) ? 'selected="selected"' : '';
						$soptions .= sprintf($this->tags['selectoption'],
								$k, $selected, $v);
					}
					$soptions .= $this->tags['optionsend'];
				}
			}
			$element = sprintf($this->tags[$args['tag']], $this->getId(), $this->section->getPage(), $this->getName(), $soptions);
		}
		
		echo $element;
	}
	
	
	public function sanitize($inputs){
		$outputs = array();
		foreach((array) $inputs as $input){
			$outputs[] = sanitize_mime_type($input);
		}
		
		return $outputs;
	}
	
	public function getType(){
		return null;
	}
}