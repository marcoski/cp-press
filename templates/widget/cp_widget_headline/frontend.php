<?php
	echo $args['before_widget'];
	if(isset($instance['font']['css_import'])){
		echo '<style type="text/css">' . $instance['font']['css_import'] . '</style>';
	}
	
	$styles = array();
	if($instance['font']['family'] != '' && $instance['font']['family'] != 'default'){
		$styles['font-family'] = $instance['font']['family'];
	}
	if($instance['font']['weight'] != '' && $instance['font']['family'] != 'default'){
		$styles['font-weight'] = $instance['font']['weight'];
	}
	if($instance['align'] != ''){
		$styles['text-align'] = $instance['align'];
	}
	if($instance['color'] != ''){
		$styles['color'] = $instance['color'];
	}
	$headContainerClasses = $filter->apply('cppress_widget_heading_container_classes', $instance['containerclass'], $instance);
	if($headContainerClasses !== ''){
		echo '<div class="'.$headContainerClasses.'">';
	}
	$headClasses = $filter->apply('cppress_widget_heading_classes', $instance['titleclass'], $instance);
	$haedStyles = $filter->apply('cppress_widget_heading_styles', $styles, $instance);
	echo '<' . $instance['htag'];
	echo !empty($headClasses) ? ' class="'. implode(' ', $headClasses) . '"' : '';
	if(!empty($styles)){
		echo ' style="';
		foreach($styles as $key => $val){
			echo $key . ':' . $val . '; ';
		}
		echo '"';
	}
	echo '>';
	echo $filter->apply('cppress_widget_heading_before_title', '', $instance['wtitle'], $instance['htag']);
	echo $instance['wtitle'];
	echo $filter->apply('cppress_widget_heading_after_title', '', $instance['wtitle'], $instance['htag']);
	echo '</' . $instance['htag'] . '>';
	if($headContainerClasses !== ''){
		echo '</div>';
	}
	echo $args['after_widget'];