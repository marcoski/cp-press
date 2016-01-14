<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'rsslink' ); ?>"><?php _e('Enter the RSS feed URL here:', 'cppress') ?></label>
	<input class="widefat" type="text"  
		id="<?= $widget->get_field_id( 'rsslink' ); ?>" 
		name="<?= $widget->get_field_name( 'rsslink' ); ?>" 
		value="<?= esc_attr($instance['rsslink']) ?>">
</div>
<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress') ?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= esc_attr($instance['wtitle']); ?>"
  />
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'rssitems' ); ?>"><?php _e('Items would you like to display', 'cppress'); ?></label>
	<input class="widefat" type="number" min="1" max="10" 
		id="<?= $widget->get_field_id( 'rssitems' ); ?>" 
		name="<?= $widget->get_field_name( 'rssitems' ); ?>" 
		value="<?= esc_attr($instance['rssitems']) ?>">
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'rssshowsummary' ); ?>">
		<input type="checkbox"
			 id="<?= $widget->get_field_id( 'rssshowsummary' ); ?>" 
			 name="<?= $widget->get_field_name( 'rssshowsummary' ); ?>"  
			 value="1" <?php checked( $instance['rssshowsummary'], 1 ); ?>>
		<?php _e('Display item content', 'cppress'); ?>
	</label>
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'rssshowauthor' ); ?>">
		<input type="checkbox"
			 id="<?= $widget->get_field_id( 'rssshowauthor' ); ?>" 
			 name="<?= $widget->get_field_name( 'rssshowauthor' ); ?>" 
			 value="1" <?php checked( $instance['rssshowauthor'], 1 ); ?>>
		<?php _e('Display item author if available', 'cppress'); ?>
	</label>
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'rssshowdate' ); ?>">
		<input type="checkbox" 
			id="<?= $widget->get_field_id( 'rssshowdate' ); ?>" 
			name="<?= $widget->get_field_name( 'rssshowdate' ); ?>" 
			value="1" <?php checked( $instance['rssshowdate'], 1 ); ?>>
		<?php _e('Display item date', 'cppress'); ?>
	</label>
</div>