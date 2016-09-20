<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

class DomainControllerField extends UrlField{
	
	public function render(array $args){
		parent::render($args);
		//echo ' <button id="cppress-detect-port" class="button button-primary">Detect Port</button> ';
	}
	
}