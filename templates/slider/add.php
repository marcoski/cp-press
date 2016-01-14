<?= $media ?>
<div class="cp-widget-field">
    <label for="<?= $id; ?>_title">
    	<?php _e('Title', 'cppress')?>
    </label>
    <input type="text" class="widefat"
		name="<?= $name; ?>[title][]" 
		value="<? $values != '' ? e($values['title']) : e('') ?>"/>
</div>
<?= $linker ?>
<?= $editor ?>