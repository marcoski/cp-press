<?php
	echo $args['before_widget'];
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title',
				'<h4>' .$instance['wtitle'].'</h4>', $instance['wtitle']);
	}
	
	$figClasses = $filter->apply('cppress_widget_media_figure_classes', array('figure'), $instance['wtitle'], $instance);
	echo '<figure class=" ' . implode(' ', $figClasses) . '">';
		$imgClasses = $filter->apply('cppress_widget_media_image_classes', array('figure-img', 'img-responsive', 'img-rounded'), $instance['wtitle'], $instance);
		echo '<img src="' . $instance['link'] . '" class="' . implode(' ', $imgClasses) . '" />';
	if(isset($instance['showcaption']) && $instance['showcaption']){
		$figCapClasses = $filter->apply('cppress_widget_media_figurecaption_classes', array('figure-caption'), $instance['wtitle'], $instance);
		echo '<figcaption class="' . implode(' ', $figCapClasses) . '">'.$instance['alttext'].'</figcaption>';
	}
	echo '</figure>';
	
	echo $args['after_widget'];
?>