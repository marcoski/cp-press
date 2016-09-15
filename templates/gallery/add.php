<div class="cp-widget-field">
    <label for="<?= $id; ?>_title">
    	<?php _e('Image Caption', 'cppress')?>
    </label>
    <?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'name' => $name.'[caption][]',
		'value' => isset($values['caption']) ? $values['caption'] : ''
	), $values, 'caption');
	?>
	<input class="widefat"
    <?php foreach($attrs as $name => $value){
		echo ' '.$name.'="'.$value.'"';
   	}?>
	/>
</div>
<?= $image ?>
<?= $video ?>