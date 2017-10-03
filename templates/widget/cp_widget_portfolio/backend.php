<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <?php echo $template->inc('/templates/widget/widget-parts/wtitle', 
  		array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
</div>
<?= $repeater ?>
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
			<label><?php _e('Items per row', 'cppress'); ?></label>
			<input type="number" min="1" max="6"
				id="<?= $widget->get_field_id( 'itemperrow' ); ?>"
				name="<?= $widget->get_field_name( 'itemperrow' ); ?>" 
				value="<?= $instance['itemperrow'] ?>"/>
		</div>
		<div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'linktitle' ); ?>">
	    	<input class="widefat"
		      id="<?= $widget->get_field_id( 'linktitle' ); ?>"
		      name="<?= $widget->get_field_name( 'linktitle' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['linktitle'] ); ?> />&nbsp;
	    	<?php _e('Link title', 'cppress')?>
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