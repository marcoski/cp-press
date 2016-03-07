<?php
use Commonhelp\Util\Inflector;
wp_nonce_field('save', '_cppress_multilanguage_nonce');
echo '<select name="cp-press-country">';
foreach($flags as $img){
	$code = strtoupper(basename($img, '.svg'));
	if(isset($countries[$code])){
		$country = Inflector::humanize(strtolower($countries[$code]), ' ');
		echo '<option value="' . strtolower($code) . '"' . selected($selectedCountry, strtolower($code)) . '>' . $country . '</option>';
	}
}
echo '</select>';