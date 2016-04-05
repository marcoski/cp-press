<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Text', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'htag' ); ?>"><?php _e('H Tag', 'cppress')?>:</label>
  <select 
  	id="<?= $widget->get_field_id( 'htag' ); ?>"
  	name="<?= $widget->get_field_name( 'htag' ); ?>">
			<option value="h1" <?php selected($instance['htag'], 'h1'); ?>><?php _e('Heading 1', 'cppress') ?></option>
			<option value="h2" <?php selected($instance['htag'], 'h2'); ?>><?php _e('Heading 2', 'cppress') ?></option>
			<option value="h3" <?php selected($instance['htag'], 'h3'); ?>><?php _e('Heading 3', 'cppress') ?></option>
			<option value="h4" <?php selected($instance['htag'], 'h4'); ?>><?php _e('Heading 4', 'cppress') ?></option>
			<option value="h5" <?php selected($instance['htag'], 'h5'); ?>><?php _e('Heading 5', 'cppress') ?></option>
			<option value="h6" <?php selected($instance['htag'], 'h6'); ?>><?php _e('Heading 6', 'cppress') ?></option>
	</select>
</div>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-field-color">
			<label for="<?= $id ?>_icolor"><?php _e('Color', 'cppress')?>:</label>
			<input class="wp-color-picker" 
				id="<?= $widget->get_field_id( 'color' ); ?>" 
				name="<?= $widget->get_field_name( 'color' ); ?>" 
				value="<?= $instance['color'] ?>">
		</div>
		<?php echo $fonts; ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'align' ); ?>"><?php _e('Align', 'cppress')?>:</label>
		  <select 
		  	id="<?= $widget->get_field_id( 'align' ); ?>"
		  	name="<?= $widget->get_field_name( 'align' ); ?>">
					<option value="left" <?php selected($instance['align'], 'left'); ?>><?php _e('Left', 'cppress') ?></option>
					<option value="right" <?php selected($instance['align'], 'right'); ?>><?php _e('Right', 'cppress') ?></option>
					<option value="center" <?php selected($instance['align'], 'center'); ?>><?php _e('Center', 'cppress') ?></option>
					<option value="justify" <?php selected($instance['align'], 'justify'); ?>><?php _e('Justify', 'cppress') ?></option>
			</select>
		</div>
	</div>
</div>