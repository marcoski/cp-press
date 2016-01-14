<div class="cp-widget-field">
    <label for="<?= $id; ?>_slides">
    	<?php _e('Slide text', 'cppress')?>
    </label>
    <input type="text" class="widefat"
		name="<?= $name; ?>[slides][]" 
		value="<? $values['slides'] != '' ? e($values['slides']) : e('') ?>"/>
</div>