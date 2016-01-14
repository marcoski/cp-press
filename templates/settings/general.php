<?php 

$options = $_settings->getOption();
if(!$options){
	$options = array();
}else{
	$options = $options['general'];
}
?>
<table class="form-table">
	<thead>
		<tr>
			<th colspan="2"><h4>Javascript Settings</h4></th>
		</tr>
	</thead>
	<tr valign="top">
		<th scope="row">Menu slider offset (px)</th>
		<td>
			<input type="text" name="cppress_settings_options[general][menu_slider_offset]" 
				   value="<?php !empty($options) ? e(esc_attr($options['general']['menu_slider_offset'])) : e('100') ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Scroll Adjust positions (px)</th>
		<td>
			<input type="text" name="cppress_settings_options[general][scroll_top_offset][min]" 
				   value="<?php !empty($options) ? e(esc_attr($options['general']['scroll_top_offset']['min'])) : e('10') ?>" />
			to <input type="text" name="cppress_settings_options[general][scroll_top_offset][max]" 
				   value="<?php !empty($options) ? e(esc_attr($options['general']['scroll_top_offset']['max'])) : e('100') ?>" />
		</td>
	</tr>
</table>
<table class="form-table">
	<thead>
		<tr>
			<th colspan="2"><h4>Style Settings</h4></th>
		</tr>
	</thead>
	<tr valign="top">
		<th scope="row">Menu Strip Background</th>
		<td>
			<input type="text" class="chpress_header_colorpick" name="cppress_settings_options[general][color][menu_background][color]" 
				   value="<?php !empty($options) ? e(esc_attr($options['general']['color']['menu_background']['color'])) : e('#000000') ?>" /><br /><br />
			Use inline style <input name="cppress_settings_options[general][color][menu_background][usecss]" type="checkbox" value="1" <?php checked(!empty($options) ? $options['general']['color']['menu_background']['usecss'] : '0', '1'); ?> />&nbsp;
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Menu Text Color</th>
		<td>
			<input type="text" class="chpress_header_colorpick" name="cppress_settings_options[general][color][menu_text_color][color]" 
				   value="<?php !empty($options) ? e(esc_attr($options['general']['color']['menu_text_color']['color'])) : e('#FFFFFF') ?>" /><br /><br />
			Use inline style <input name="cppress_settings_options[general][color][menu_text_color][usecss]" type="checkbox" value="1" <?php checked(!empty($options) ? $options['general']['color']['menu_text_color']['usecss'] : '0', '1'); ?> />&nbsp;
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Menu Hover Line Color</th>
		<td>
			<input type="text" class="chpress_header_colorpick" name="cppress_settings_options[general][color][menu_hover_line_color][color]" 
				   value="<?php !empty($options) ? e(esc_attr($options['general']['color']['menu_hover_line_color']['color'])) : e('#00b5a5') ?>" /><br /><br />
			Use inline style <input name="cppress_settings_options[general][color][menu_hover_line_color][usecss]" type="checkbox" value="1" <?php checked(!empty($options) ? $options['general']['color']['menu_hover_line_color']['usecss'] : '0', '1'); ?> />&nbsp;
			</td>
	</tr>
</table>
<table class="form-table">
	<thead>
		<tr>
			<th colspan="2"><h4>Misc Settings</h4></th>
		</tr>
	</thead>
	<tr valign="top">
		<th scope="row">Logo</th>
		<td><label for="cppress_logo">
			<?php
				$logoImgUri = !empty($options) ? $options['chpress_header_settings']['cppress_logo'] : $root.'/img/chpress.png';
			?>
			<img src="<?php e($logoImgUri) ?>" id="cppress_logo_img" />
			<input id="cppress_logo" type="hidden" name="cppress_settings_options[general][cppress_logo]" value="<?php e(esc_attr($logoImgUri)); ?>" />
			<input id="cppress_logo_button" type="button" value="Upload" />
			<br />Upload an image for the banner.
			</label>
		</td>
	</tr>
</table>