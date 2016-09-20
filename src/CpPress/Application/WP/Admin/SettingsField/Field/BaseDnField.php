<?php
namespace CpPress\Application\WP\Admin\SettingsField\Field;

class BaseDnField extends TextField{
	
	public function render(array $args){
		parent::render($args);
		//echo ' <button id="cppress-detect-basedn" class="button button-primary">Detect Base DN</button> ';
		//echo '<button id="cppress-test-basedn" class="button button-primary">Test Base DN</button> ';
	}
	
}