<?php 
	echo $args['before_widget'];
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title',
				'<h4>' .$instance['wtitle'].'</h4>', $instance['wtitle']);
	}
	
	$figClasses = $filter->apply('cppress_widget_media_figure_classes', array(), $instance['wtitle'], $instance);
	echo '<figure class=" ' . implode(' ', $figClasses) . '">';
		$imgClasses = $filter->apply('cppress_widget_media_image_classes', array('img-responsive'), $instance['wtitle'], $instance);
		echo '<img src="' . $instance['link'] . '" class="' . implode(' ', $imgClasses) . '" />';
	if(isset($instance['showcaption']) && $instance['showcaption']){
		echo '<figcaption>'.$instance['wtitle'].'</figcaption>';
	}
	echo '</figure>';
	
	echo $args['after_widget'];
?>