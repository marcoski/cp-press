<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <?php echo $template->inc('/templates/widget/widget-parts/wtitle', 
  		array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
</div>
<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'mapcenter' ); ?>"><?php _e('Map center', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'mapcenter' ); ?>"
    name="<?= $widget->get_field_name( 'mapcenter' ); ?>"
    value="<?= $instance['mapcenter']; ?>"
  />
  <div class="cp-widget-field-description">
		<?php _e( 'The name of a place, town, city, or even a country. Can be an exact address too.', 'cppress' ); ?>
	</div>
</div>

<!-- MARKER -->
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Markers', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<?php echo $media; ?>
		<?php echo $repeater; ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'maptype' ); ?>"><?php _e('When should Info Windows be displayed?', 'cppress')?>:</label>
		  <select 
		  	id="<?= $widget->get_field_id( 'infodisplay' ); ?>"
		  	name="<?= $widget->get_field_name( 'infodisplay' ); ?>">
					<option value="click" <?php selected($instance['infodisplay'], 'click'); ?>><?php _e('Click', 'cppress') ?></option>
					<option value="mouseover" <?php selected($instance['infodisplay'], 'mouseover'); ?>><?php _e('Mouse over', 'cppress') ?></option>
					<option value="always" <?php selected($instance['infodisplay'], 'always'); ?>><?php _e('Always', 'cppress') ?></option>
			</select>
		</div>
	</div>
</div>

<!-- STYLES -->
<div class="cp-widget-field cp-widget-type-section cp-widget-gmaps-styles">
	<label class="section"><?php _e('Style', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input cp-widget-gmap-mapstyles">
		  <label for="<?= $widget->get_field_id( 'mapstyles' ); ?>"><?php _e('Map styles', 'cppress')?>:</label>
		  <select 
		  	id="<?= $widget->get_field_id( 'mapstyles' ); ?>"
		  	name="<?= $widget->get_field_name( 'mapstyles' ); ?>">
					<option value="normal" <?php selected($instance['mapstyles'], 'normal'); ?>><?php _e('Default', 'cppress') ?></option>
					<option value="custom" <?php selected($instance['mapstyles'], 'custom'); ?>><?php _e('Custom', 'cppress') ?></option>
					<option value="rawjson" <?php selected($instance['mapstyles'], 'rawjson'); ?>><?php _e('Predefined styles', 'cppress') ?></option>
			</select>
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'styledmapname' ); ?>"><?php _e('Styled map name', 'cppress')?>:</label>
		  <input class="widefat"
		    id="<?= $widget->get_field_id( 'styledmapname' ); ?>"
		    name="<?= $widget->get_field_name( 'styledmapname' ); ?>"
		    value="<?= $instance['styledmapname']; ?>"
		  />
		</div>
		<div class="cp-widget-gmaps-custom cp-widget-section-hide">
			<?php echo $customStyles; ?>
		</div>
		<div class="cp-widget-field cp-widget-input cp-widget-gmaps-rawjson cp-widget-section-hide">
		  <label for="<?= $widget->get_field_id( 'rawjsonmapstyles' ); ?>"><?php _e('Raw JSON styles', 'cppress')?>:</label>
		  <textarea 
		  	type="text" 
		  	id="<?= $widget->get_field_id( 'rawjsonmapstyles' ); ?>"
		    name="<?= $widget->get_field_name( 'rawjsonmapstyles' ); ?>"
		  	class="widefat" 
		  	rows="5"><?= $instance['rawjsonmapstyles']; ?></textarea>
		</div>
		<div class="cp-widget-field-description">
			<?php _e('Copy and paste predefined styles here from', 'cppress'); ?> 
			<a href="http://snazzymaps.com/" target="_blank">Snazzy Maps</a>.
		</div>
	</div>
</div>

<!-- OPTIONS -->
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'maptype' ); ?>"><?php _e('Map type', 'cppress')?>:</label>
		  <select 
		  	id="<?= $widget->get_field_id( 'maptype' ); ?>"
		  	name="<?= $widget->get_field_name( 'maptype' ); ?>">
					<option value="interactive" <?php selected($instance['maptype'], 'interactive'); ?>><?php _e('Interactive', 'cppress') ?></option>
					<option value="static" <?php selected($instance['maptype'], 'static'); ?>><?php _e('Static', 'cppress') ?></option>
			</select>
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'mapwidth' ); ?>"><?php _e('Map Width', 'cppress')?>:</label>
		  <input class="widefat"
		    id="<?= $widget->get_field_id( 'mapwidth' ); ?>"
		    name="<?= $widget->get_field_name( 'mapwidth' ); ?>"
		    value="<? echo $instance['mapwidth'] == '' ? '640' : esc_attr($instance['mapwidth']);  ?>"
		  />
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'mapheight' ); ?>"><?php _e('Map Height', 'cppress')?>:</label>
		  <input class="widefat"
		    id="<?= $widget->get_field_id( 'mapheight' ); ?>"
		    name="<?= $widget->get_field_name( 'mapheight' ); ?>"
		    value="<? echo $instance['mapheight'] == '' ? '480' : esc_attr($instance['mapheight']);  ?>"
		  />
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'mapzoom' ); ?>"><?php _e( 'Map zoom:', 'cppress' ); ?></label>
			<input type="number" min="0" max="21"
				id="<?= $widget->get_field_id( 'mapzoom' ); ?>" 
				name="<?= $widget->get_field_name( 'mapzoom' ); ?>" 
				value="<? echo $instance['mapzoom'] == '' ? '7' : esc_attr( $instance['mapzoom'] ); ?>" />
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'scrolltozoom' ); ?>">
				<input type="checkbox" 
					id="<?= $widget->get_field_id( 'scrolltozoom' ); ?>" 
					name="<?= $widget->get_field_name( 'scrolltozoom' ); ?>" 
					value="1" <?php if ( $instance['scrolltozoom'] ) { ?>checked="1"<?php } ?> />
				<?php _e( 'Scroll to zoom', 'cppress' ); ?>
			</label>
			<div class="cp-widget-field-description">
				<?php _e( 'Allow scrolling over the map to zoom in or out.', 'cppress' ); ?>
			</div>	
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'draggable' ); ?>">
				<input type="checkbox" 
					id="<?= $widget->get_field_id( 'draggable' ); ?>" 
					name="<?= $widget->get_field_name( 'draggable' ); ?>" 
					value="1" <?php if ( $instance['draggable'] ) { ?>checked="1"<?php } ?> />
				<?php _e( 'Draggable Map', 'cppress' ); ?>
			</label>
			<div class="cp-widget-field-description">
				<?php _e( 'Allow dragging the map to move it around.' ); ?>
			</div>	
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'disableui' ); ?>">
				<input type="checkbox" 
					id="<?= $widget->get_field_id( 'disableui' ); ?>" 
					name="<?= $widget->get_field_name( 'disableui' ); ?>" 
					value="1" <?php if ( $instance['disableui'] ) { ?>checked="1"<?php } ?> />
				<?php _e( 'Disable default UI', 'cppress' ); ?>
			</label>
			<div class="cp-widget-field-description">
				<?php _e( 'Hides the default Google Maps controls.' ); ?>
			</div>	
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'keepcenter' ); ?>">
				<input type="checkbox" 
					id="<?= $widget->get_field_id( 'keepcenter' ); ?>" 
					name="<?= $widget->get_field_name( 'keepcenter' ); ?>" 
					value="1" <?php if ( $instance['keepcenter'] ) { ?>checked="1"<?php } ?> />
				<?php _e( 'Keeps the map centered when it\'s container is resized.', 'cppress' ); ?>
			</label>
			<div class="cp-widget-field-description">
				<?php _e( 'Hides the default Google Maps controls.' ); ?>
			</div>	
		</div>
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