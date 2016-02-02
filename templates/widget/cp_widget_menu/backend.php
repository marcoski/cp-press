<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'menu' ); ?>"><?php _e('Select navigation menu', 'cppress')?>:</label>
  <select 
  	id="<?= $widget->get_field_id( 'navmenu' ); ?>"
  	name="<?= $widget->get_field_name( 'navmenu' ); ?>">
		<? foreach($menues as $id => $menu): ?>
			<option value="<?= $id ?>" <?php selected($instance['navmenu'], $id); ?>><?= $menu ?></option>
		<? endforeach; ?>
	</select>
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