<?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'id' => $widget['id'] . '_subtitle',
		'name' => $widget['name'] . '[subtitle]',
		'value' => isset($instance['parallax']['subtitle']) ? $instance['parallax']['subtitle'] : ''
	), $instance, 'subject');
?>
<input class="widefat"
    <?php foreach($attrs as $name => $value){
		echo ' '.$name.'="'.$value.'"';
    }?>
/>