<?php
$attrs = $filter->apply('cppress_widget_attrs', array(
    'id' => $widget->get_field_id( 'linkbuttontext' ),
    'name' => $widget->get_field_name( 'linkbuttontext' ),
    'value' => $instance['linkbuttontext']
), $instance, 'linkbuttontext');
?>
<input
    placeholder="<?php _e('Insert link button text...', 'cppress'); ?>"
    <?php foreach($attrs as $name => $value){
        echo ' '.$name.'="'.$value.'"';
    }?>
/>