<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?php echo $widget->get_field_id( 'twitter_screen_name' ); ?>"><?php _e( 'Twitter Screen Name:', 'cppress' ); ?></label>
	<input type="text" 
	id="<?php echo $widget->get_field_id( 'twitter_screen_name' ); ?>" 
	name="<?php echo $widget->get_field_name( 'twitter_screen_name' ); ?>" 
	value="<?php echo esc_attr( $instance['twitter_screen_name'] ); ?>" />
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
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Twitter Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $widget->get_field_id( 'twitter_theme' ); ?>"><?php _e( 'Twitter Widget Theme:', 'cppress' ); ?></label>
			<select id="<?php echo $widget->get_field_id( 'twitter_theme' ); ?>" name="<?= $widget->get_field_name( 'twitter_theme' ); ?>">
				<option value="light" <?php selected($instance['twitter_theme'], 'light'); ?>><?php _e('Light', 'cppress'); ?></option>
				<option value="dark" <?php selected($instance['twitter_theme'], 'dark'); ?>><?php _e('Dark', 'cppress'); ?></option>
			</select>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $widget->get_field_id( 'twitter_tweet_limit' ); ?>"><?php _e( 'Tweet Limit:', 'cppress' ); ?></label>
				<input type="number" min="0" max="20"
           id="<?php echo $widget->get_field_id( 'twitter_tweet_limit' ); ?>" 
           name="<?php echo $widget->get_field_name( 'twitter_tweet_limit' ); ?>"
           value="<?php echo $instance['twitter_tweet_limit'] ?>"> 
		</div>

		<div class="cp-widget-field cp-widget-input">
	    <label for="<?php echo $widget->get_field_id( 'twitter_show_replies' ); ?>">
	    	<input
		      id="<?php echo $widget->get_field_id( 'twitter_show_replies' ); ?>"
		      name="<?php echo $widget->get_field_name( 'twitter_show_replies' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['twitter_show_replies'] ); ?> />&nbsp;
	    	<?php _e('Show title', 'cppress')?>
	    </label>
		</div>

		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $widget->get_field_id( 'twitter_width' ); ?>"><?php _e( 'Twitter Widget Width:', 'cppress' ); ?></label>
			<input type="number" min="180" max="520"
				id="<?php echo $widget->get_field_id( 'twitter_width' ); ?>" 
				name="<?php echo $widget->get_field_name( 'twitter_width' ); ?>"
				value="<?php echo $instance['twitter_width'] ?>"> 
		</div>

		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $widget->get_field_id( 'twitter_height' ); ?>"><?php _e( 'Twitter Widget Height:', 'cppress' ); ?></label>
			<input type="number" min="180" max="520"
				id="<?php echo $widget->get_field_id( 'twitter_height' ); ?>" 
				name="<?php echo $widget->get_field_name( 'twitter_height' ); ?>"
				value="<?php echo $instance['twitter_height'] ?>">
		</div>
		<div class="cp-widget-field cp-widget-field-color">
			<label for="<?php echo $widget->get_field_id( 'twitter_link_color' ); ?>"><?php _e( 'Twitter Widget Link Color:', 'cppress' ); ?> </label>
			<input class="wp-color-picker" 
				id="<?php echo $widget->get_field_id( 'twitter_link_color' ); ?>" 
				name="<?php echo $widget->get_field_name( 'twitter_link_color' ); ?>" 
				value="<?php echo esc_attr( $instance['twitter_link_color'] ); ?>">
		</div>
		<div class="cp-widget-field cp-widget-input">
		 <label for="<?php echo $widget->get_field_id( 'twitter_chrome_header' ); ?>">
	    	<input
		      id="<?php echo $widget->get_field_id( 'twitter_chrome_header' ); ?>"
		      name="<?php echo $widget->get_field_name( 'twitter_chrome_header' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['twitter_chrome_header'] ); ?> />&nbsp;
	    	<?php _e( 'Show Twitter Widget Header:', 'cppress' ); ?>
	    </label>
		</div>

		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $widget->get_field_id( 'twitter_chrome_footer' ); ?>">
	    	<input
		      id="<?php echo $widget->get_field_id( 'twitter_chrome_footer' ); ?>"
		      name="<?php echo $widget->get_field_name( 'twitter_chrome_footer' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['twitter_chrome_footer'] ); ?> />&nbsp;
	    	<?php _e( 'Show Twitter Widget Footer:', 'cppress' ); ?>
	    </label>
		</div>

		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $widget->get_field_id( 'twitter_chrome_border' ); ?>">
	    	<input
		      id="<?php echo $widget->get_field_id( 'twitter_chrome_border' ); ?>"
		      name="<?php echo $widget->get_field_name( 'twitter_chrome_border' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['twitter_chrome_border'] ); ?> />&nbsp;
	    	<?php _e( 'Show Twitter Widget Border:', 'cppress' ); ?>
	    </label>
		</div>

		<div class="cp-widget-field cp-widget-field-color">
			<label for="<?php echo $widget->get_field_id( 'twitter_border_color' ); ?>"><?php _e( 'Twitter Widget Border Color:', 'cppress' ); ?></label>
			<input class="wp-color-picker" 
				id="<?php echo $widget->get_field_id( 'twitter_border_color' ); ?>" 
				name="<?php echo $widget->get_field_name( 'twitter_border_color' ); ?>" 
				value="<?php echo esc_attr( $instance['twitter_border_color'] ); ?>">
		</div>

		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $widget->get_field_id( 'twitter_chrome_scrollbar' ); ?>">
	    	<input
		      id="<?php echo $widget->get_field_id( 'twitter_chrome_scrollbar' ); ?>"
		      name="<?php echo $widget->get_field_name( 'twitter_chrome_scrollbar' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['twitter_chrome_scrollbar'] ); ?> />&nbsp;
	    	<?php _e( 'Show Twitter Widget Scrollbar:', 'cppress' ); ?>
	    </label>
		</div>

		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $widget->get_field_id( 'twitter_chrome_background' ); ?>">
	    	<input
		      id="<?php echo $widget->get_field_id( 'twitter_chrome_background' ); ?>"
		      name="<?php echo $widget->get_field_name( 'twitter_chrome_background' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['twitter_chrome_background'] ); ?> />&nbsp;
	    	<?php _e( 'Use Twitter Widget Background Color:', 'cppress' ); ?>
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