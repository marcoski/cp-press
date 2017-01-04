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
	<?php
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'id' => $widget['form']['id'],
		'name' => $widget['form']['name'],
		'class' => "large-text code",
		'cols' => "100",
		'rows' => "14"
	), $instance, 'form');
	?>
	<textarea
		<?php foreach($attrs as $name => $value){
			echo ' '.$name.'="'.$value.'"';
		}?>
	>
	<?php echo $filter->apply('cppress_widget_content', $instance['form'], $instance); ?>
	</textarea>
</div>