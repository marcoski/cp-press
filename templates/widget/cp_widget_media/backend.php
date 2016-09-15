<?= $media ?>
<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <?php echo $template->inc('/templates/widget/widget-parts/wtitle', 
  		array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
</div>
<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'alttext' ); ?>"><?php _e('Alt Text', 'cppress')?>:</label>
  <?php echo $template->inc('/templates/widget/widget-parts/alttext', 
  		array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
</div>
<?= $link ?>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field">
			<label for="<?= $widget->get_field_id( 'opennewwindow' ); ?>">
				<input 
					id="<?= $widget->get_field_id( 'opennewwindow' ); ?>"
					name="<?= $widget->get_field_name( 'opennewwindow' ); ?>" 
					type="checkbox" 
					value="1" <?php checked( '1', $instance['opennewwindow'] ); ?> />&nbsp;
				<?php _e('Open in new window', 'cppress'); ?>
			</label>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $widget->get_field_id('showcaption' ); ?>">
				<input 
					id="<?= $widget->get_field_id( 'showcaption' ); ?>"
					name="<?= $widget->get_field_name( 'showcaption' ); ?>" 
					type="checkbox" 
					value="1" <?php checked( '1', $instance['showcaption'] ); ?> />&nbsp;
				<?php _e('Show caption', 'cppress'); ?>
				<div class="cp-widget-field-description">
					<?php _e('Show title in figcaption.', 'cppress')?>
				</div>
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