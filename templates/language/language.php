<?php
use Commonhelp\Util\Inflector;
wp_nonce_field('save', '_cppress_multilanguage_nonce');
echo '<select name="cp-press-country">';
foreach($flags as $img){
	$code = basename($img, '.svg');
	if(isset($languages[$code])){
		$lang = $languages[$code];
		echo '<option value="' . strtolower($code) . '"' . selected($selectedCountry, strtolower($code)) . '>' . $lang . '</option>';
	}
}
echo '</select>';