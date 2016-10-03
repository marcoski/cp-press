<?php

if(isset($instance['icon']) && $instance['icon'] != ''){
    $iconContainerOpen = '';
    $iconContainerClose = '';
    if(isset($instance['iconclass']) && $instance['iconclass'] !== ''){
        $iconContainerOpen = '<div class="' . $instance['iconclass'] . '">';
        $iconContainerClose = '</div>';
    }
    echo $filter->apply('cppress_widget_icon_container_open', $iconContainerOpen, $instance);
    $icon = $filter->apply('cppress_widget_icon', $instance['icon'], $instance);
    if(false !== strpos($icon, 'fontawesome')){
        $icon = 'fa '.$icon;
        $icon = preg_replace("/fontawesome-(.*)/", "fa-$1", $icon);
    }
    echo '<i class="' . $icon . '"></i>';
    echo $filter->apply('cppress_widget_icon_container_close', $iconContainerClose, $instance);
}