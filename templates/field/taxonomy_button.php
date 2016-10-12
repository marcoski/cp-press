<div class="cp-widget-field cp-widget-field-type-link cp-widget-field-type-taxonomy cp-widget-field-url" data-excluded-taxonomies="<?= $excluded_taxonomies ?>">
	<label for="<?= $id ?>"><?php _e('Archive URL', 'cppress')?></label>
	<a href="#" class="select-content-button button-secondary"><?php _e('Select Taxonomy', 'cppress')?></a>
	<div class="existing-content-selector">
		<input type="text" class="content-text-search" placeholder="<?php _e('Search taxonomy', 'cppress')?>">
		<ul class="posts"></ul>
		<div class="buttons">
			<a href="#" class="button-close button-secondary"><?php _e('Close', 'cppress')?></a>
		</div>
	</div>
	<div class="url-input-wrapper">
		<input type="text" class="widefat" id="<?= $id ?>" name="<?= $name ?>" value="<?= $value ?>" >
	</div>
</div>