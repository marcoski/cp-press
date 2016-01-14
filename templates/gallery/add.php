<div class="cp-widget-field">
    <label for="<?= $id; ?>_title">
    	<?php _e('Image Caption', 'cppress')?>
    </label>
    <input type="text" class="widefat"
		name="<?= $name; ?>[caption][]" 
		value="<? !empty($values) ? e($values['caption']) : e('') ?>"/>
</div>
<?= $image ?>
<?= $video ?>