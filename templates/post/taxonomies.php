<div class="cp-widget-field cp-widget-input">
	<label for="<?= $id ?>_<?= $taxonomy[0] ?>_<?= $count ?>"><?php _e('Select element to filter', 'cppress')?>:</label>
	<?php $taxonomiesValue = get_terms($taxonomy['term'], array('hide_empty' => false)); ?>
  <select 
  	id="<?= $id ?>_<?= $taxonomy[0] ?>_<?= $count ?>"
   	name="<?= $name ?>[<?= $taxonomy[0] ?>][<?= $count - 1 ?>]">
  	<option value=""></option>
  	<?php foreach($taxonomiesValue as $key => $value): ?>
  	<option value="<?= $value->term_id ?>" 
  		<?php if($values[$taxonomy[0]] == $value->term_id) echo 'selected="selected"'; ?>><?= $value->name ?></option>
  	<?php endforeach; ?>
	</select>
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $id ?>_<?= $taxonomy[1] ?>_<?= $count ?>">
		<input type="checkbox" 
			id="<?= $id ?>_<?= $taxonomy[1] ?>_<?= $count ?>" 
			name="<?= $name ?>[<?= $taxonomy[1] ?>][<?= $count - 1 ?>]" 
			value="1" <?php if ( $values[$taxonomy[1]] ) { ?>checked="1"<?php } ?> />
		<?php _e( 'Exclude', 'cppress' ); ?>
	</label>	
</div>