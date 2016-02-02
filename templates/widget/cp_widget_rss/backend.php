<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress') ?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= esc_attr($instance['wtitle']); ?>"
  />
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'rsslink' ); ?>"><?php _e('Enter the RSS feed URL here:', 'cppress') ?></label>
	<input class="widefat" type="text"  
		id="<?= $widget->get_field_id( 'rsslink' ); ?>" 
		name="<?= $widget->get_field_name( 'rsslink' ); ?>" 
		value="<?= esc_attr($instance['rsslink']) ?>">
</div>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('RSS Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'rssitems' ); ?>"><?php _e('Items would you like to display', 'cppress'); ?></label>
			<input class="widefat" type="number" min="1" max="10" 
				id="<?= $widget->get_field_id( 'rssitems' ); ?>" 
				name="<?= $widget->get_field_name( 'rssitems' ); ?>" 
				value="<?= esc_attr($instance['rssitems']) ?>">
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'showauthor' ); ?>">
				<input type="checkbox"
					 id="<?= $widget->get_field_id( 'showauthor' ); ?>" 
					 name="<?= $widget->get_field_name( 'showauthor' ); ?>" 
					 value="1" <?php checked( $instance['showauthor'], 1 ); ?>>
				<?php _e('Display item author if available', 'cppress'); ?>
			</label>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'showdate' ); ?>">
				<input type="checkbox" 
					id="<?= $widget->get_field_id( 'showdate' ); ?>" 
					name="<?= $widget->get_field_name( 'showdate' ); ?>" 
					value="1" <?php checked( $instance['showdate'], 1 ); ?>>
				<?php _e('Display item date', 'cppress'); ?>
			</label>
		</div>
	</div>
</div>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		 <div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'showtitle' ); ?>">
	    	<input class="widefat"
		      id="<?= $widget->get_field_id( 'showtitle' ); ?>"
		      name="<?= $widget->get_field_name( 'showtitle' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['showtitle'] ); ?> />&nbsp;
	    	<?php _e('Show title', 'cppress')?>
	    </label>
	  </div>
	</div>
</div>