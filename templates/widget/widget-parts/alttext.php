<?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'id' => $widget->get_field_id( 'alttext' ),
		'name' => $widget->get_field_name( 'alttext' ),
		'value' => $instance['alttext']
	), $instance, 'wtitle');
?>
<input class="widefat"
    <?php foreach($attrs as $name => $value){
		echo ' '.$name.'="'.$value.'"';
    }?>
/>