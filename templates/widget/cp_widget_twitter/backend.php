<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'username' ); ?>"><?php _e( 'Twitter Username', 'cppress' ); ?></label>
  <input class="widefat" type="text"
  	id="<?= $widget->get_field_id( 'username' ); ?>" 
  	name="<?= $widget->get_field_name( 'username' ); ?>"
  	value="<?= esc_attr( $istance['username'] ); ?>">
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'twitterid' ); ?>"><?php _e( 'Twitter Id', 'cppress' ); ?> (<?php _e('Required', 'cppress'); ?>)</label>
	<input class="widefat" type="text"
		id="<?= $widget->get_field_id( 'twitterid' ); ?>" 
		name="<?php echo $widget->get_field_name( 'twitterid' ); ?>" 
		value="<?php echo esc_attr( $instance['twitterid']); ?>">
  <div class="cp-widget-field-description">
  	<?php _e('Get Your Twitter Widget Id', 'cppress') ?>: 
  	<a href="https://dev.twitter.com/discussions/20722" target="_blank"><?php _e('Here', 'cppress') ?></a>
  </div>       
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'theme' ); ?>"><?php _e( 'Theme', 'cppress' ); ?></label>
	<select id="<?= $widget->get_field_id( 'theme' ); ?>" name="<?= $widget->get_field_name( 'theme' ); ?>">
		<option value="light" <?php selected($instance['theme'], 'light'); ?>><?php _e('Light', 'cppress'); ?></option>
		<option value="dark" <?php selected($instance['theme'], 'dark'); ?>><?php _e('Dark', 'cppress'); ?></option>
	</select>
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'width' ); ?>"><?php _e( 'Width', 'cppress' ); ?></label>
  <input type="text"
  	id="<?= $widget->get_field_id( 'width' ); ?>" 
  	name="<?= $widget->get_field_name( 'width' ); ?>"
  	value="<?= esc_attr( $istance['width'] ); ?>">
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'height' ); ?>"><?php _e( 'Height', 'cppress' ); ?></label>
  <input type="text"
  	id="<?= $widget->get_field_id( 'height' ); ?>" 
  	name="<?= $widget->get_field_name( 'height' ); ?>"
  	value="<?= esc_attr( $istance['height'] ); ?>">
</div>
<div class="cp-widget-field cp-widget-field-color">
	<label for="<?= $widget->get_field_id( 'iconcolor' ); ?>"><?php _e('Icon color', 'cppress')?>:</label>
	<input class="wp-color-picker" 
		id="<?= $widget->get_field_id( 'iconcolor' ); ?>" 
		name="<?= $widget->get_field_name( 'iconcolor' ); ?>" 
		value="<?= $instance['iconcolor']; ?>">
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'excludereplies' ); ?>">
    <input
      id="<?= $widget->get_field_id( 'excludereplies' ); ?>"
      name="<?= $widget->get_field_name( 'excludereplies' ); ?>"
      type="checkbox"
      value="1" <?php checked( '1', $instance['excludereplies'] ); ?> />&nbsp;
      <?php _e('Exclude Replies on Tweets', 'cppress')?>
   </label>
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'autoexpandphotos' ); ?>">
    <input
      id="<?= $widget->get_field_id( 'autoexpandphotos' ); ?>"
      name="<?= $widget->get_field_name( 'autoexpandphotos' ); ?>"
      type="checkbox"
      value="1" <?php checked( '1', $instance['excludereplies'] ); ?> />&nbsp;
      <?php _e('Auto Expand Photos in Tweets', 'cppress')?>
   </label>
</div>
