<?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'id' => $widget['submit']['id'],
		'name' => $widget['submit']['name'],
		'value' => $instance['submit'] != '' ? $instance['submit'] : __('Contact Us', 'cppress')
	), $instance, 'subject');
?>
<input class="widefat"
    <?php foreach($attrs as $name => $value){
		echo ' '.$name.'="'.$value.'"';
    }?>
/>