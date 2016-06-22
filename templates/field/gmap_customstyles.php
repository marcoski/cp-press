<div class="cp-widget-field cp-widget-input">
  <select 
  	id="<?= $id ?>_mapfeature_<?= $count ?>"
  	name="<?= $name ?>[mapfeature][]">
  	<?php foreach($gmapsStyleOptions['mapfeature'] as $key => $value): ?>
  	<option value="<?= $key ?>" <?php selected($values['mapfeature'], $key); ?>><?= $value ?></option>
  	<?php endforeach; ?>
	</select>
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $id ?>_elementtype_<?= $count ?>"><?php _e('Select element type to style', 'cppress')?>:</label>
  <select 
  	id="<?= $id ?>_elementtype_<?= $count ?>"
  	name="<?= $name ?>[elementtype][]">
  	<?php foreach($gmapsStyleOptions['elementtype'] as $key => $value): ?>
  	<option value="<?= $key ?>" <?php selected($values['elementtype'], $key); ?>><?= $value ?></option>
  	<?php endforeach; ?>
	</select>
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $id ?>_visible_<?= $count ?>">
		<input type="checkbox" 
			id="<?= $id ?>_visible_<?= $count ?>" 
			name="<?= $name ?>[visible][]" 
			value="1" <?php if ( $values['visible'] ) { ?>checked="1"<?php } ?> />
		<?php _e( 'Visible', 'cppress' ); ?>
	</label>	
</div>
<div class="cp-widget-field cp-widget-field-color">
	<label for="<?= $id ?>_color_<?= $count ?>"><?php _e('Color', 'cppress')?>:</label>
	<input class="wp-color-picker" 
		id="<?= $id ?>_color_<?= $count ?>" 
		name="<?= $name ?>[color][]" 
		value="<?= $values['color'] ?>">
</div>