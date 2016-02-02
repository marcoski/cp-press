<?php
	echo $args['before_widget'];
	$mainClass = $instance['maincontainerclass'] != '' ? 'class="' . $instance['maincontainerclass'] . '"' : '';
	echo $filter->apply('cppress_widget_text_maincontainer_open' ,'<div '. $mainClass .'>', $instance['wtitle']);
	if(isset($instance['icon']) && $instance['icon'] != ''){
		echo $filter->apply('cppress_widget_icon_container_open', '', $instance);
		$icon = $filter->apply('cppress_widget_icon', $instance['icon'], $instance);
		echo '<i class="' . $icon . '"></i>';
		echo $filter->apply('cppress_widget_icon_container_close', '', $instance['containerclass'], $instance);
	}
	echo $filter->apply('cppress_widget_text_container_open', '', $instance);
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title', 
				'<h1>' .$instance['wtitle'].'</h1>', $instance['wtitle']);
	}
	echo $filter->apply('cppress_widget_text_before_the_content', '', $instance);
	$text = $instance['text'];
	if(isset($instance['removep']) && $instance['removep']){
		$text = wpautop($text);
	}else{
		$text = wpautop($text, false);
	}
	echo $filter->apply('cppress_widget_text_the_content', $text, $instance);
	echo $filter->apply('cppress_widget_text_after_the_content', '', $instance);
	echo $filter->apply('cppress_widget_text_container_close', '', $instance['containerclass'], $instance);
	echo $filter->apply('cppress_widget_text_maincontainer_close', '</div>', $instance['wtitle']);
	echo $args['after_widget'];