<?php
	echo $args['before_widget'];
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title',
				'<h1>' .$instance['wtitle'].'</h1>', $instance['wtitle']);
	}
	
	if($instance['maptype'] === 'static'){
		echo $filter->apply('cppress_widget_gmaps_static', 
				'<img border="0" src="' . esc_url($mapsrc) . '">', $mapsrc);
	}else{
		$mapId = $filter->apply('cppress_widget_gmap_id', md5($instance['mapcenter']), $instance['mapcenter']);
		$mapClasses = $filter->apply('cppress_widget_gmap_classes', array('cp-google-map-canvas'), $mapId);
		$mapAttrs = $filter->apply('cppress_gmap_slider_attrs', array(
				'id' => $mapId,
				'class' => implode(' ', $mapClasses),
				'style' => 'height:' . intval($instance['mapheight']) . 'px;',
				'data-options' => esc_attr(json_encode($mapData))
		), $mapId);
		echo '<div';
		foreach($mapAttrs as $name => $value){
			echo ' ' . $name . '="' . $value . '"';
		}
		echo '></div>';
	}
	
	echo $args['after_widget'];