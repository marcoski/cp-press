<?php
	echo $args['before_widget'];
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title',
				'<h1>' .$instance['wtitle'].'</h1>', $instance['wtitle']);
	}
	$containerClasses = $filter->apply('cppress_widget_socialbutton_container_classes',
			array('cp-widget-button-container', 'text-'.$instance['align']), $instance['wtitle']);
	echo $filter->apply('cppresss_widget_socialbutton_container_open',
			 '<div class="' . implode(' ', $containerClasses) . '">', $instance['wtitle']);
	foreach($instance['networks'] as $n => $network){
		echo $filter->apply('cppress_widget_socialbutton_before', '', $instance['wtitle'], $network);
		$defaultBtnClasses = array();
		if(isset($instance['hovereffects']) && $instance['hovereffects']){
			$defaultBtnClasses[] = 'btn-hover';
		}
		$buttonClasses = $filter->apply('cppress_widget_socialbutton_classes',
				$defaultBtnClasses, $instance['wtitle'], $network);
		
		$defaultBtnAttrs = array(
				'class' => implode(' ', $buttonClasses),
				'href' => $network['url']
		);
		
		if($network['style'] != ''){
			$defaultBtnAttrs['style'] = $network['style'];
		}
		$buttonAttrs = $filter->apply('cppress_widget_socialbutton_attrs', $defaultBtnAttrs, $instance['wtitle'], $network);
		echo '<a';
		foreach($buttonAttrs as $name => $value){
			echo ' ' . $name . '="' . $value . '"';
		}
		echo '>';
		$iconClasses = $filter->apply('cppress_widget_socialbutton_icon_classes', 
				array('fontawesome-' . $network['icon']), $instance['wtitle'], $network);
		echo '<i class="' . implode(' ', $iconClasses) . '"></i>';
		echo '</a>';
		echo $filter->apply('cppress_widget_socialbutton_after', '', $instance['wtitle'], $network);
	}
	echo $filter->apply('cppress_widget_socialbutton_container_close', '</div>', $instance['wtitle']);
	echo $args['after_widget'];
