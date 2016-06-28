<div class="cp-widget-field cp-widget-input">
 <select 
  	id="<?= $id ?>_network"
  	name="<?= $name ?>[network][]">
  	<option value="default" disabled="disabled" selected="selected">Select network</option>
		<? foreach($networks as $network => $label): ?>
			<option value="<?= $network ?>" <?php selected($values['network'], $network); ?>><?= $label['label'] ?></option>
		<? endforeach; ?>
	</select>
</div>
<div class="cp-widget-field cp-widget-input">
  <label for="<?= $id ?>_url"><?php _e('URL', 'cppress')?>:</label>
  <input class="widefat"
   	id="<?= $id ?>_url" name="<?= $name ?>[url][]" value="<?= $values['url'] ?>"
  />
</div>
<div class="cp-widget-field cp-widget-field-color">
	<label for="<?= $id ?>_icolor"><?php _e('Icon color', 'cppress')?>:</label>
	<input class="wp-color-picker" id="<?= $id ?>_icolor" name="<?= $name ?>[icolor][]" value="<?= $values['icolor'] ?>">
</div>
<div class="cp-widget-field cp-widget-field-color">
	<label for="<?= $id ?>_bgcolor"><?php _e('Background color', 'cppress')?>:</label>
	<input class="wp-color-picker" id="<?= $id ?>_bgcolor" name="<?= $name ?>[bgcolor][]" value="<?= $values['bgcolor'] ?>">
</div>