<?php 
	echo $args['before_widget'];
	
	$containerClasses = $filter->apply('cppress_widget_button_container_classes', 
			array('cp-widget-button-container', 'text-'.$instance['align']), $instance['wtitle']);
	echo '<div class="' . implode(' ', $containerClasses) . '">';
	$defaultBtnClasses = array('btn', 'btn-default');
	if(isset($instance['hovereffects']) && $instance['hovereffects']){
		$defaultBtnClasses[] = 'btn-hover';
	}
	$buttonClasses = $filter->apply('cppress_widget_button_classes', 
			$defaultBtnClasses, $instance['wtitle']);
	
	$defaultBtnAttrs = array(
		'class' => implode(' ', $buttonClasses),
		'href' => $instance['link']
	);
	
	if($style != ''){
		$defaultBtnAttrs['style'] = $style;
	}
	$buttonAttrs = $filter->apply('cppress_widget_button_attrs', $defaultBtnAttrs, $instance['wtitle']);
	echo '<a';
	foreach($buttonAttrs as $name => $value){
		echo ' ' . $name . '="' . $value . '"';
	} 
	echo '>';
	echo $instance['wtitle'];
	echo '</a>';
	echo '</div>';
	
	echo $args['after_widget'];