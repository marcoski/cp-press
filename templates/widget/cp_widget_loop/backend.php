<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<?= $advanced; ?>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		 <div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'paginate' ); ?>">
	    	<input class="widefat"
		      id="<?= $widget->get_field_id( 'paginate' ); ?>"
		      name="<?= $widget->get_field_name( 'paginate' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['paginate'] ); ?> />&nbsp;
	    	<?php _e('Paginate', 'cppress'); ?>
	    </label>
	  </div>
		 <div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'paginateajax' ); ?>">
	    	<input class="widefat"
		      id="<?= $widget->get_field_id( 'paginateajax' ); ?>"
		      name="<?= $widget->get_field_name( 'paginateajax' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['paginateajax'] ); ?> />&nbsp;
	    	<?php _e('Ajax Paginate', 'cppress'); ?>
	    </label>
	  </div>
	  <div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'ajaxbutton' ); ?>"><?php _e('Load ajax text', 'cppress')?>:</label>
		  <input class="widefat"
		    id="<?= $widget->get_field_id( 'ajaxbutton' ); ?>"
		    name="<?= $widget->get_field_name( 'ajaxbutton' ); ?>"
		    value="<?= $instance['ajaxbutton']; ?>"
		  />
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget->get_field_id( 'templatename' ); ?>"><?php _e('Custom template name', 'cppress')?>:</label>
		  <input class="widefat"
		    id="<?= $widget->get_field_id( 'templatename' ); ?>"
		    name="<?= $widget->get_field_name( 'templatename' ); ?>"
		    value="<?= $instance['templatename']; ?>"
		  />
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