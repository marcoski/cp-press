<div class="cp-widget-field cp-widget-field-type-link cp-widget-field-url" data-valid-types="<?= $valid_types ?>">		
	<label for="<?= $id ?>"><?php _e('Destination URL', 'cppress')?></label>
	<a href="#" class="select-content-button button-secondary"><?php _e('Select Content', 'cppress')?></a>
	<div class="existing-content-selector">
		<input type="text" class="content-text-search" placeholder="<?php _e('Search content', 'cppress')?>">
		<ul class="posts"></ul>
		<div class="buttons">
			<a href="#" class="button-close button-secondary"><?php _e('Close', 'cppress')?></a>
		</div>
	</div>
	<div class="url-input-wrapper">
		<input type="text" class="widefat" id="<?= $id ?>" name="<?= $name ?>" value="<?= $value ?>" >
	</div>
</div>