<?php

$mapId = $filter->apply('cppress_widget_gmap_id', $mapid, $mapdata);
$mapClasses = $filter->apply('cppress_widget_gmap_classes', array('cp-google-map-canvas'), $mapId);
$mapAttrs = $filter->apply('cppress_gmap_slider_attrs', array(
		'id' => $mapId,
		'class' => implode(' ', $mapClasses),
		'style' => 'height:' . intval($options['mapheight']) . 'px;',
		'data-options' => esc_attr($mapdata)
), $mapId);
echo '<div';
foreach($mapAttrs as $name => $value){
	echo ' ' . $name . '="' . $value . '"';
}
echo '></div>';