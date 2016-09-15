<?= $media ?>
<div class="cp-widget-field">
    <label for="<?= $id; ?>_title">
    	<?php _e('Title', 'cppress')?>
    </label>
    <?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'name' => $name.'[title][]',
		'value' => isset($values['title']) ? $values['title'] : ''
	), $values, 'title');
	?>
	<input class="widefat"
    <?php foreach($attrs as $name => $value){
		echo ' '.$name.'="'.$value.'"';
   	}?>
	/>
</div>
<?= $linker ?>
<?= $editor ?>