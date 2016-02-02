<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<?= $accordion ?>
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Controls', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'stheme' ); ?>"><?php _e('Slider theme', 'cppress')?>:</label>
		  <select 
		  	id="<?= $widget->get_field_id( 'stheme' ); ?>"
		  	name="<?= $widget->get_field_name( 'stheme' ); ?>">
					<option value="bootstrap" <?php selected($instance['stheme'], 'bootstrap'); ?>><?php _e('Bootstrap', 'cppress') ?></option>
					<option value="flexer" <?php selected($instance['stheme'], 'flexer'); ?>><?php _e('Flexer', 'cppress') ?></option>
			</select>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label><?php _e('Animation speed', 'cppress'); ?></label>
			<input type="text" class="widefat"
				id="<?= $widget->get_field_id( 'speed' ); ?>"
				name="<?= $widget->get_field_name( 'speed' ); ?>" 
				value="<? isset($instance['speed']) ? e($instance['speed']) : e('800') ?>"/>
			<div class="cp-widget-field-description">
				<?php _e('Animation speed in milliseconds', 'cppress')?>.
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label><?php _e('Timeout', 'cppress'); ?></label>
			<input type="text" class="widefat"
				id="<?= $widget->get_field_id( 'timeout' ); ?>"
				name="<?= $widget->get_field_name( 'timeout' ); ?>" 
				value="<? isset($instance['timeout']) ? e($instance['timeout']) : e('8000') ?>"/>
			<div class="cp-widget-field-description">
				<?php _e('How long each frame is displayed for in milliseconds', 'cppress')?>.
			</div>
		</div>
		<div class="cp-widget-field cp-widget-field-color">
			<label for="<?= $id['navcolor'] ?>"><?php _e('Navigation Color', 'cppress')?>:</label>
     	<input class="wp-color-picker" 
     		id="<?= $widget->get_field_id( 'navcolor' ); ?>" 
     		name="<?= $widget->get_field_name( 'navcolor' ); ?>" 
     		value="<?= $instance['navcolor']; ?>">
		</div>
		<div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'hidecontrol' ); ?>">
	    	<input class="widefat"
		      id="<?= $widget->get_field_id( 'hidecontrol' ); ?>"
		      name="<?= $widget->get_field_name( 'hidecontrol' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['hidecontrol'] ); ?> />&nbsp;
	    	<?php _e('Hide left and right control', 'cppress')?>
	    </label>
	  </div>
	  <div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'hideindicators' ); ?>">
	    	<input class="widefat"
		      id="<?= $widget->get_field_id( 'hideindicators' ); ?>"
		      name="<?= $widget->get_field_name( 'hideindicators' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['hideindicators'] ); ?> />&nbsp;
	    	<?php _e('Hide control indicators', 'cppress')?>
	    </label>
	  </div>
	</div>
</div>
<div class="cp-widget-field cp-widget-type-section">
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