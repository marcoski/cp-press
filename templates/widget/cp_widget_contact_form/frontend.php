<?php 
	echo $args['before_widget'];
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title',
				'<h4>' .$instance['wtitle'].'</h4>', $instance['wtitle']);
	}
	
	echo $widget;
	
	echo $args['after_widget'];
?>