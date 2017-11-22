<?php 
	echo $args['before_widget'];
	$containerClasses = $filter->apply('cppress_widget_button_container_classes', 
			array('cp-widget-button-container', 'text-'.$instance['align']), $instance['wtitle']);
	echo '<div class="' . implode(' ', $containerClasses) . '">';
	$defaultBtnClasses = array('btn');
	if(isset($instance['btheme']) && $instance['btheme'] !== ''){
	    $defaultBtnClasses[] = $instance['btheme'];
    }else{
	    $defaultBtnClasses[] = 'btn-default';
    }
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
    if ( ! isset( $instance['iconposition'] )
        || ($instance['iconposition'] === 'top'
            || $instance['iconposition'] === 'before-title'
            || $instance['iconposition'] === 'before-content') ) {
        echo $template->inc( '/templates/widget/widget-parts/frontend/icon', array(
            'instance' => $instance,
            'filter'   => $filter
        ) );
    }
	echo $instance['wtitle'];
    if ( isset( $instance['iconposition'] )
        && ($instance['iconposition'] === 'after-content'
            || $instance['iconposition'] === 'after-title'
            || $instance['iconposition'] === 'bottom') ) {
        echo $template->inc( '/templates/widget/widget-parts/frontend/icon', array(
            'instance' => $instance,
            'filter'   => $filter
        ) );
    }
	echo '</a>';
	echo '</div>';
	
	echo $args['after_widget'];