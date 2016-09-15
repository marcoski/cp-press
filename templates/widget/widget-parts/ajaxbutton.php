<?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'id' => $widget->get_field_id( 'ajaxbutton' ),
		'name' => $widget->get_field_name( 'ajaxbutton' ),
		'value' => $instance['ajaxbutton']
	), $instance, 'wtitle');
?>
<input class="widefat"
    <?php foreach($attrs as $name => $value){
		echo ' '.$name.'="'.$value.'"';
    }?>
/>