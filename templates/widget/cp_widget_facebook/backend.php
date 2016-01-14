<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'numberofposts' ); ?>"><?php _e( 'Number of posts:', 'cppress' ); ?></label>
	<input type="number" min="1" max="99"
		id="<?= $widget->get_field_id( 'numberofposts' ); ?>" 
		name="<?= $widget->get_field_name( 'numberofposts' ); ?>"  
		value="<?= esc_attr( $instance['numberofposts'] ); ?>" />
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'excerptlength' ); ?>"><?php _e( 'Excerpt length:', 'cppress' ); ?></label>
	<input type="number" min="1" max="9999"
		id="<?= $widget->get_field_id( 'excerptlength' ); ?>" 
		name="<?= $widget->get_field_name( 'excerptlength' ); ?>" 
		value="<?= esc_attr( $instance['excerptlength'] ); ?>" />
</div>	
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'showlikecount' ); ?>">
		<input type="checkbox" 
			id="<?= $widget->get_field_id( 'showlikecount' ); ?>" 
			name="<?php echo $widget->get_field_name( 'showlikecount' ); ?>" 
			value="1" <?php checked( $instance['showlikecount'], 1 ); ?>  />
		<?php _e( 'Show like count', 'cppress' ); ?>
	</label>
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'showcommentcount' ); ?>">
		<input type="checkbox" 
			id="<?= $widget->get_field_id( 'showcommentcount' ); ?>" 
			name="<?= $widget->get_field_name( 'showcommentcount' ); ?>" 
			value="1" <?php checked( $instance['showcommentcount'], 1 ); ?> />
		<?php _e( 'Show comment count', 'cppress' ); ?>
	</label>
</div>
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'showpagelink' ); ?>">
		<input type="checkbox" 
			id="<?= $widget->get_field_id( 'showpagelink' ); ?>" 
			name="<?= $widget->get_field_name( showpagelink ); ?>" 
			value="1" <?php if ( $instance['showpagelink'] ) { ?>checked="1"<?php } ?> />
		<?php _e( 'Show link to Facebook page', 'cppress' ); ?>
	</label>
</div>	
<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'showlinkpreviews' ); ?>">
		<input type="checkbox" 
			id="<?= $widget->get_field_id( 'showlinkpreviews' ); ?>" 
			name="<?= $widget->get_field_name( 'showlinkpreviews' ); ?>" 
			value="1" <?php if ( $instance['showlinkpreviews'] ) { ?>checked="1"<?php } ?> />
		<?php _e( 'Show link previews', 'cppress' ); ?>
	</label>	
</div>
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Facebook settings', 'cppress')?></label>
	<div class="cp-widget-section">
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'fbapp' ); ?>"><?php _e('Facebook App ID/API Key', 'cppress'); ?></label>
			<input type="text" class="widefat" placeholder="Eg: 123456789012345"
				id="<?= $widget->get_field_id( 'fbapp' ); ?>"
				name="<?= $widget->get_field_id( 'fbapp' ); ?>" 
				value="<?= esc_attr($instance['fbapp']) ?>"/>
			<div class="cp-widget-field-description">
				<?php printf( __( 'Get your App ID from %s.', 'cppress' ), '<a href="https://developers.facebook.com/apps">developers.facebook.com/apps</a>' ); ?>
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'fbappsecret' ); ?>"><?php _e('Facebook App Secret', 'cppress'); ?></label>
			<input type="text" class="widefat" placeholder="Eg: 16vgrz4hk45wvh29k2puk45wvk2h29pu"
				id="<?= $widget->get_field_id( 'fbappsecret' ); ?>"
				name="<?= $widget->get_field_id( 'fbappsecret' ); ?>" 
				value="<?= esc_attr($instance['fbappsecret']) ?>"/>
			<div class="cp-widget-field-description">
				<?php printf( __( 'Get your App Secret from %s.', 'cppress' ), '<a href="https://developers.facebook.com/apps">developers.facebook.com/apps</a>' ); ?>
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'fbid' ); ?>"><?php _e('Facebook Page ID or Slug', 'cppress'); ?></label>
			<input type="text" class="widefat" placeholder="Eg: CommonHelp"
				id="<?= $widget->get_field_id( 'fbid' ); ?>"
				name="<?= $widget->get_field_id( 'fbid' ); ?>" 
				value="<?= esc_attr($instance['fbid']) ?>"/>
			<div class="cp-widget-field-description">
				<?php printf( __( 'Use <a href="%s">this tool</a> to find the numeric ID of your Facebook page.', 'cppress' ), 'http://findmyfacebookid.com' ); ?>
			</div>
		</div>
	</div>
</div>
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Appearance', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'linktext' ); ?>"><?php _e( 'Link text', 'cppress' ); ?></label>
			<input type="text" class="widefat" placeholder="<?php _e( 'Find us on Facebook', 'cppress' ); ?>"
				id="<?= $widget->get_field_id( 'linktext' ); ?>"
				name="<?= $widget->get_field_id( 'linktext' ); ?>" 
				value="<?= esc_attr($instance['linktext']) ?>"/>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'loadcss' ); ?>">
				<input
		      id="<?= $widget->get_field_id( 'loadcss' ); ?>"
		      name="<?= $widget->get_field_name( 'loadcss' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance[ 'loadcss'] ); ?> />&nbsp;
				<?php _e( 'Load some default styles?', 'cppress' ); ?>
			</label>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'imagesize' ); ?>"><?php _e('Image size', 'cppress'); ?></label>
			<select 
				id="<?= $widget->get_field_id( 'imagesize' ); ?>" 
				name="<?= $widget->get_field_name( 'imagesize' ); ?>">
				<option value="dont_show" <?php selected( $instance['imgsize'], 'dont_show' ); ?>><?php _e("Don't show images", 'cppress'); ?></option>
				<option value="thumbnail" <?php selected( $instance['imgsize'], 'thumbnail' ); ?>><?php _e('Thumbnail', 'cppress'); ?></option>
				<option value="normal" <?php selected( $instance['imgsize'], 'normal' ); ?>><?php _e('Normal', 'cppress'); ?></option>
			</select>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label><?php _e('Image dimensions', 'cppress'); ?></label>
			<label style="float:left; margin-right:20px; ">
				<?php _e('Max Width', 'cppress'); ?><br />
				<input type="number" min="0" max="1600" size="3" 
					id="<?= $widget->get_field_id( 'imgwidth' ); ?>" 
					name="<?= $widget->get_field_id( 'imgwidth' ); ?>" 
					value="<?php echo esc_attr( $instance['imgwidth'] ); ?>" />
			</label>
			<label style="float:left; margin-right:20px;">
				<?php _e('Max Height', 'cppress'); ?><br />
				<input type="number" min="0" max="1600" size="3" 
					id="<?= $widget->get_field_id( 'imgheight' ); ?>" 
					name="<?= $widget->get_field_id( 'imgheight' ); ?>" 
					value="<?php echo esc_attr( $instance['imgheight'] ); ?>" />
			</label>
			<div class="cp-widget-field-description">
				<?php _e( '(in pixels, optional)', 'cppress' ); ?><br />
				<?php _e( 'Leave empty for default sizing', 'cppress' ); ?>
			</div>
		</div>
</div>