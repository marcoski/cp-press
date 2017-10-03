<?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'id' => $widget->get_field_id( 'wtitle' ),
		'name' => $widget->get_field_name( 'wtitle' ),
		'value' => $instance['wtitle']
	), $instance, 'wtitle');
?>
<input class="widefat"
    <?php foreach($attrs as $name => $value){
		echo ' '.$name.'="'.$value.'"';
    }?>
/>