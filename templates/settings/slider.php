<?php 

$options = $_settings->getOption();
if(!$options){
	$options = array();
}else{
	$options = $options['slider'];
}

?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">Slider</th>
		<td>
			<select class="widefat" id="slider_type_select" name="cppress_settings_options[slider][type]">	
				<option value="cppress" <?php selected(!empty($options) ? $options['slider']['type'] : false, 'cppress'); ?>>CpPress Slider</option>
				<option value="parallax" <?php selected(!empty($options) ? $options['slider']['type'] : false, 'parallax'); ?>>Parallax Overlay (Sug. Codeon Theme)</option>
				<option value="bootstrap" <?php selected(!empty($options) ? $options['slider']['type'] : false, 'bootstrap'); ?>>Bootstrap Carousel (Not developed yet)</option>
			</select>
		</td>
	</tr>
</table>
<br />
<h4>General Options</h4>
<table class="form-table">
	<tr valign="top">
		<th scope="row">Image width</th>
		<td>
			<input type="text" name="cppress_settings_options[slider][imgwidth]" 
				   value="<?php !empty($options) ? e(esc_attr($options['slider']['imgwidth'])) : e('1920') ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Image height</th>
		<td>
			<input type="text" name="cppress_settings_options[slider][imgheight]" 
				   value="<?php !empty($options) ? e(esc_attr($options['slider']['imgheight'])) : e('900') ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Translate time (in ms)</th>
		<td>
			<input type="text" name="cppress_settings_options[slider][translatetime]" 
				   value="<?php !empty($options) ? e(esc_attr($options['slider']['translatetime'])) : e('5000') ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Show time (in ms)</th>
		<td>
			<input type="text" name="cppress_settings_options[slider][showtime]" 
				   value="<?php !empty($options) ? e(esc_attr($options['slider']['showtime'])) : e('1000') ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Slider Logo</th>
		<td><label for="cppress_slider_logo">
			<?php
				$logoImgUri = !empty($options) ? $options['slider']['cppress_slider_logo'] : get_template_directory_uri().'/img/logo_slide.png';
			?>
			<img src="<?php e($logoImgUri) ?>" id="cppress_slider_logo_img" />
			<input id="cppress_slider_logo" type="hidden" name="cppress_settings_options[slider][cppress_slider_logo]" value="<?php e(esc_attr($logoImgUri)); ?>" />
			<input id="cppress_slider_logo_button" type="button" value="Upload" />
			</label>
		</td>
	</tr>
</table>
<br />
<div id="cppress-box" class="hideable">
	<h3>CpPress Slider Options</h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Center Logo</th>
			<td>
				<input name="cppress_settings_options[slider][center_logo]" type="checkbox" value="1" <?php checked(!empty($options) ? $options['slider']['center_logo'] : '0', '1'); ?> />&nbsp;
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Logo position top</th>
			<td>
					<input name="cppress_settings_options[slider][logo_ptop]" type="checkbox" value="1" <?php checked(!empty($options) ? $options['slider']['logo_ptop'] : '0', '1'); ?> />&nbsp;
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Logo position bottom</th>
			<td>
				<input name="cppress_settings_options[slider][logo_pbottom]" type="checkbox" value="1" <?php checked( !empty($options) ? $options['slider']['logo_pbottom'] : '0', '1'); ?> />&nbsp;
			</td>
		</tr>
	</table>
</div>
<div id="parallax-box" class="hideable">
	<h3>Parallax Slider Options</h3>
	<table class="form-table">
		
	</table>
</div>
<div id="bootstrap-box" class="hideable">
	<h3>Bootstrap Carousel Options</h3>
	<table class="form-table">
		
	</table>
</div>