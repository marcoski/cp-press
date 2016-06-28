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
	
	$headClasses = $filter->apply('cppress_widget_heading_classes', array('test'), $instance);
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
	echo $instance['wtitle'];
	echo '</' . $instance['htag'] . '>';
	echo $args['after_widget'];