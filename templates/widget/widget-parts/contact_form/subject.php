<?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'id' => $widget['subject']['id'],
		'name' => $widget['subject']['name'],
		'value' => $instance['subject']
	), $instance, 'subject');
?>
<input class="widefat"
    <?php foreach($attrs as $name => $value){
		echo ' '.$name.'="'.$value.'"';
    }?>
/>