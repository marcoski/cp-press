<?php
/** @var \CpPress\Application\Widgets\CpWidgetBase $widget */
$widget = $widget;
$templates = $widget->getWidgetTemplates();

?>

<label for="<?= $widget->get_field_id( 'templatename' ); ?>"><?php _e('Custom template name', 'cppress')?>:</label>

<?php if(empty($templates)): ?>
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
<?php else: ?>
    <select
        id="<?php echo $widget->get_field_id('templatename') ?>"
        name="<?php echo $widget->get_field_name('templatename') ?>"
        class="cp-template-name-field" style="width: 400px;"
    >
        <option
                value=""
                data-template="<?php echo htmlspecialchars(json_encode([], JSON_HEX_TAG)) ?>" >
            <?php echo $template['title'] ?>
        </option>
        <?php foreach($templates as $template): ?>
            <?php if(isset($instance['templatename']) && $instance['templatename'] === $template['file']): ?>
                <option
                        selected="selected"
                        value="<?php echo $template['file'] ?>"
                        data-template="<?php echo htmlspecialchars(json_encode($template, JSON_HEX_TAG)) ?>" >
                    <?php echo $template['title'] ?>
                </option>
            <?php else: ?>
                <option
                        value="<?php echo $template['file'] ?>"
                        data-template="<?php echo htmlspecialchars(json_encode($template, JSON_HEX_TAG)) ?>" >
                    <?php echo $template['title'] ?>
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
    <script type="application/javascript">
        var $ = jQuery;
        $(document).on('widget.loaded', function(){
            $('.cp-template-name-field').select2({
                allowClear: true,
                width: 'element',
                placeholder: "Select a template for this widget",
                templateResult: function(state){
                    if(!state.id){
                        return state.text;
                    }
                    var $option = $(state.element);
                    var templateData = $option.data('template');

                    var $state = $(
                        '<span>'+state.text+'<span class="cp-item-template-description">'+templateData.description+'</span></span>'
                    );

                    return $state;
                }
            });
        });
    </script>
<?php endif; ?>
