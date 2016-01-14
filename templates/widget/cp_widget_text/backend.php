<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<?= $editor ?>
<?= $icon; ?>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'maincontainerclass' ); ?>"><?php _e('Container class', 'cppress')?>:</label>
	    <input class="widefat"
	      id="<?= $widget->get_field_id( 'maincontainerclass' ); ?>"
	      name="<?= $widget->get_field_name( 'maincontainerclass' ); ?>"
	      value="<? isset($instance['maincontainerclass']) ? e($instance['maincontainerclass']) : e(''); ?>"/>
	  </div>
	  <div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'containerclass' ); ?>"><?php _e('Text container class', 'cppress')?>:</label>
	    <input class="widefat"
	      id="<?= $widget->get_field_id( 'containerclass' ); ?>"
	      name="<?= $widget->get_field_name( 'containerclass' ); ?>"
	      value="<? isset($instance['containerclass']) ? e($instance['containerclass']) : e(''); ?>"/>
	  </div>
	  <div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'doshortcode' ); ?>">
	    	<input class="widefat"
	      id="<?= $widget->get_field_id( 'doshortcode' ); ?>"
	      name="<?= $widget->get_field_name( 'doshortcode' ); ?>"
	      type="checkbox"
	      value="1" <?php checked( '1', $instance['doshortcode'] ); ?> />&nbsp;
	      <?php _e('Apply shortcode', 'cppress')?>
	    </label>
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