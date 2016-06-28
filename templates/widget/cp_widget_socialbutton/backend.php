<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<?= $repeater; ?>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Design and Layout', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'opennew' ); ?>">
	    	<input class="widefat"
	      id="<?= $widget->get_field_id( 'opennew' ); ?>"
	      name="<?= $widget->get_field_name( 'opennew' ); ?>"
	      type="checkbox"
	      value="1" <?php checked( '1', $instance['opennew'] ); ?> />&nbsp;
	      <?php _e('Open in new window', 'cppress')?>
	    </label>
	  </div>
	 <!-- <div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'btheme' ); ?>"><?php _e('Button theme', 'cppress')?>:</label>
		  <select 
		  	id="<?= $widget->get_field_id( 'btheme' ); ?>"
		  	name="<?= $widget->get_field_name( 'btheme' ); ?>">
					<option value="atom" <?php selected($instance['btheme'], 'atom'); ?>><?php _e('Atom', 'cppress') ?></option>
					<option value="flat" <?php selected($instance['btheme'], 'flat'); ?>><?php _e('Flat', 'cppress') ?></option>
					<option value="wire" <?php selected($instance['btheme'], 'wire'); ?>><?php _e('Wire', 'cppress') ?></option>
			</select>
		</div> -->
		<div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'hovereffects' ); ?>">
	    	<input class="widefat"
	      id="<?= $widget->get_field_id( 'hovereffects' ); ?>"
	      name="<?= $widget->get_field_name( 'hovereffects' ); ?>"
	      type="checkbox"
	      value="1" <?php checked( '1', $instance['hovereffects'] ); ?> />&nbsp;
	      <?php _e('Enable hover effects', 'cppress')?>
	    </label>
	  </div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'isize' ); ?>"><?php _e('Icon size', 'cppress')?>:</label>
		  <select 
		  	id="<?= $widget->get_field_id( 'isize' ); ?>"
		  	name="<?= $widget->get_field_name( 'isize' ); ?>">
					<option value="1" <?php selected($instance['isize'], '1'); ?>><?php _e('Normal', 'cppress') ?></option>
					<option value="1.33" <?php selected($instance['isize'], '1.33'); ?>><?php _e('Medium', 'cppress') ?></option>
					<option value="1.66" <?php selected($instance['isize'], '1.66'); ?>><?php _e('Large', 'cppress') ?></option>
					<option value="2" <?php selected($instance['isize'], '1.66'); ?>><?php _e('Extra Large', 'cppress') ?></option>
			</select>
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'rounding' ); ?>"><?php _e('Rounding', 'cppress')?>:</label>
		  <select 
		  	id="<?= $widget->get_field_id( 'rounding' ); ?>"
		  	name="<?= $widget->get_field_name( 'rounding' ); ?>">
					<option value="0" <?php selected($instance['rounding'], '0'); ?>><?php _e('None', 'cppress') ?></option>
					<option value="0.25" <?php selected($instance['rounding'], '0.25'); ?>><?php _e('Slightly rounded', 'cppress') ?></option>
					<option value="0.5" <?php selected($instance['rounding'], '0.5'); ?>><?php _e('Very rounded', 'cppress') ?></option>
					<option value="1.5" <?php selected($instance['rounding'], '1.5'); ?>><?php _e('Completely rounded', 'cppress') ?></option>
			</select>
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'padding' ); ?>"><?php _e('Padding', 'cppress')?>:</label>
		  <select 
		  	id="<?= $widget->get_field_id( 'padding' ); ?>"
		  	name="<?= $widget->get_field_name( 'padding' ); ?>">
		  		<option value="0" <?php selected($instance['padding'], '0'); ?>><?php _e('None', 'cppress') ?></option>
					<option value="0.5" <?php selected($instance['padding'], '0.5'); ?>><?php _e('Low', 'cppress') ?></option>
					<option value="1" <?php selected($instance['padding'], '1'); ?>><?php _e('Medium', 'cppress') ?></option>
					<option value="1.4" <?php selected($instance['padding'], '1.4'); ?>><?php _e('High', 'cppress') ?></option>
					<option value="1.8" <?php selected($instance['padding'], '1.8'); ?>><?php _e('Very high', 'cppress') ?></option>
			</select>
		</div>
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
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'margin' ); ?>"><?php _e('Margin', 'cppress')?>:</label>
		  <select 
		  	id="<?= $widget->get_field_id( 'margin' ); ?>"
		  	name="<?= $widget->get_field_name( 'margin' ); ?>">
		  		<option value="0" <?php selected($instance['margin'], '0'); ?>><?php _e('None', 'cppress') ?></option>
					<option value="0.1" <?php selected($instance['margin'], '0.1'); ?>><?php _e('Low', 'cppress') ?></option>
					<option value="0.2" <?php selected($instance['margin'], '0.2'); ?>><?php _e('Medium', 'cppress') ?></option>
					<option value="0.3" <?php selected($instance['margin'], '0.3'); ?>><?php _e('High', 'cppress') ?></option>
					<option value="0.4" <?php selected($instance['margin'], '1.8'); ?>><?php _e('Very high', 'cppress') ?></option>
			</select>
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