<div class="cp-widget-field cp-widget-post-select">
  <select class="widefat" name="<?= $name; ?>" id="<?= $id ?>>
		<? foreach($items as $id => $title): ?>
			<option value="<?= $id ?>" <? $id == $value ? e('selected') : e('') ?>><?= $title ?></option>
		<? endforeach; ?>
	</select>
	<div class="cp-widget-field-description">
		<?php _e('Select a post to show or advanced options', 'cppress')?>
	</div>
</div>