<div class="cp-widget-field cp-widget-field-font">
	<label for="<?= $id ?>_icolor"><?php _e('Font', 'cppress')?>:</label>
	<select name="<?php echo esc_attr($name) ?>" id="<?php echo esc_attr($id); ?>">
	<option value="default" selected="selected"><?php esc_html_e( 'Use theme font', 'cppress' ) ?></option>
	<?php foreach($fonts as $key => $val): ?>
		<option value="<?php echo esc_attr($key); ?>" <?php selected($key, $value); ?>><?php echo esc_html($val); ?></option>
	<?php endforeach; ?>
	</select>
</div>