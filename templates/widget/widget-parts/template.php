<?php
$attrs = $filter->apply('cppress_widget_attrs', array(
    'id' => $widget->get_field_id( 'templatename' ),
    'name' => $widget->get_field_name( 'templatename' ),
    'value' => $instance['templatename']
), $instance, 'templatename');
?>
<input class="widefat"
    <?php foreach($attrs as $name => $value){
        echo ' '.$name.'="'.$value.'"';
    }?>
/>