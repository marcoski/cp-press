<?php 

$options = $_settings->getOption();
if(!$options){
	$options = array();
}else{
	$options = $options['portfolio'];
}

?>
<table class="form-table">
	<thead>
		<tr>
			<th colspan="2"><h3>Main settings</h3></th>
		</tr>
	</thead>
	<tr valign="top">
		<th scope="row">Exclude Post Types</th>
		<td>
			<? foreach($post_types as $key => $post_type): ?>
			<p>
				<label class="input-checkbox"><?= $post_type ?>:</label>
				<input name="cppress_settings_options[portfolio][exclude][<?=$key?>]" type="checkbox" value="<?= $post_type ?>" <?php checked( $post_type, !empty($options) ? $options['portfolio']['exclude'][$key] : '' ); ?> />&nbsp;
			</p>
			<? endforeach ?>
		</td>
	</tr>
</table>
<table class="form-table">
	<thead>
		<tr>
			<th colspan="2"><h3>Box settings</h3></th>
		</tr>
	</thead>
	<tr valign="top">
		<th scope="row">Box Height (default is auto height)</th>
		<td>
			<input type="text" name="cppress_settings_options[portfolio][boxheight]"
				   value="<?php !empty($options) ? e(esc_attr($options['portfolio']['boxheight'])) : e('auto') ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Box slide (how much box slide up? px)</th>
		<td>
			<input type="text" name="cppress_settings_options[portfolio][boxslide]"
					value="<?php !empty($options) != '' ? e(esc_attr($options['portfolio']['boxslide'])) : e('155') ?>" />
		</td>
	</tr>
</table>