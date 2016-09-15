<?php 
	$attrs = $filter->apply('cppress_widget_attrs', array(
		'id' => $widget['body']['id'],
		'name' => $widget['body']['name'],
		'class' => "large-text code",
		'cols' => "100",
		'rows' => "4"
	), $instance, 'body');
?>
<textarea 
	<?php foreach($attrs as $name => $value){
		echo ' '.$name.'="'.$value.'"';
    }?>
>
	<?php echo $filter->apply('cppress_widget_content', $instance['body'], $instance); ?>
</textarea>