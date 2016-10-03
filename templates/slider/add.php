<?php echo $media ?>
<div class="cp-widget-field">
    <label for="<?php echo $id; ?>_title">
    	<?php _e('Title', 'cppress')?>
    </label>
    <?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'name' => $name.'[title][]',
		'value' => isset($values['title']) ? $values['title'] : ''
	), $values, 'title');
	?>
	<input class="widefat"
    <?php foreach($attrs as $n => $v){
		echo ' '.$n.'="'.$v.'"';
   	}?>
	/>
</div>
<?php echo $linker ?>
<?php echo $editor ?>
<div class="cp-widget-field cp-widget-input">
	<label for="<?php echo $id ?>_captionalign"><?php _e('Caption Align', 'cppress')?>:</label>
	<select
		id="<?php echo $id ?>_captionalign"
		name="<?php echo $name ?>[captionalign][]">
		<option value="left" <?php selected($values['align'], 'left'); ?>><?php _e('Left', 'cppress') ?></option>
		<option value="right" <?php selected($values['align'], 'right'); ?>><?php _e('Right', 'cppress') ?></option>
		<option value="center" <?php selected($values['align'], 'center'); ?>><?php _e('Center', 'cppress') ?></option>
	</select>
</div>
