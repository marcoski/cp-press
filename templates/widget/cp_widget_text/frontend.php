<?php
	echo $args['before_widget'];
	$mainClass = $instance['maincontainerclass'] != '' ? 'class="' . $instance['maincontainerclass'] . '"' : '';
	echo $filter->apply('cppress_widget_text_maincontainer_open' ,'<div '. $mainClass .'>', $instance['wtitle']);
	if(isset($instance['icon']) && $instance['icon'] != ''){
		$iconContainerOpen = '';
		$iconContainerClose = '';
		if(isset($instance['iconclass']) && $instance['iconclass'] !== ''){
			$iconContainerOpen = '<div class="' . $instance['iconclass'] . '">';
			$iconContainerClose = '</div>';
		}
		echo $filter->apply('cppress_widget_icon_container_open', $iconContainerOpen, $instance);
		$icon = $filter->apply('cppress_widget_icon', $instance['icon'], $instance);
		echo '<i class="' . $icon . '"></i>';
		echo $filter->apply('cppress_widget_icon_container_close', $iconContainerClose, $instance);
	}
	$textContainerOpen = '';
	$textContainerClose = '';
	if(isset($instance['containerclass']) && $instance['containerclass'] !== ''){
		$textContainerOpen = '<div class="' . $instance['containerclass'] . '">';
		$textContainerClose = '</div>';
	}
	echo $filter->apply('cppress_widget_text_container_open', $textContainerOpen, $instance);
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title', 
				'<h1>' .$instance['wtitle'].'</h1>', $instance['wtitle']);
	}
	echo $filter->apply('cppress_widget_text_before_the_content', '', $instance);
	$text = $instance['text'];
	
	echo $filter->apply('cppress_widget_text_the_content', $text, $instance);
	echo $filter->apply('cppress_widget_text_after_the_content', '', $instance);
	echo $filter->apply('cppress_widget_text_container_close', $textContainerClose, $instance['containerclass'], $instance);
	echo $filter->apply('cppress_widget_text_maincontainer_close', '</div>', $instance['wtitle']);
	echo $args['after_widget'];