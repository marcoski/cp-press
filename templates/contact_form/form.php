<div class="cp-widget-field cp-widget-input cp-widget-tag-generator" data-fields="<?php echo $fields_json ?>">
	<label for="<?= $widget['form']['id']; ?>"><?php _e('Form constructor', 'cppress')?>:</label>
	<div class="cp-widget-tag-generator-list">
	<?php
		foreach($fields as $key => $field){
			echo '<span 
				class="button cp-tag-generator-button cp-button-' . $key .'" 
				data-title="' . $field['title'] . '">' . $field['label'] . '</span>';
		}
	?>
	</div>
	<textarea 
		id="<?php echo $widget['form']['id']; ?>" 
		name="<?php echo $widget['form']['name']; ?>" cols="100" rows="14" class="large-text code">
		<?php echo trim($instance['form']); ?>	
	</textarea>
</div>