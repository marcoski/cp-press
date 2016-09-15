<?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'id' => $widget->get_field_id( 'linktext' ),
		'name' => $widget->get_field_name( 'linktext' ),
		'value' => $instance['linktext'];
	), $instance, 'wtitle');
?>
<input class="widefat"
    <?php foreach($attrs as $name => $value){
		echo ' '.$name.'="'.$value.'"';
    }?>
/>