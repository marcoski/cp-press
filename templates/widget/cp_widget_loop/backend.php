<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <?php echo $template->inc('/templates/widget/widget-parts/wtitle', 
  		array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
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
		  <?php echo $template->inc('/templates/widget/widget-parts/ajaxbutton', 
  			array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
		</div>
		<div class="cp-widget-field cp-widget-input">
            <?php echo $template->inc('/templates/widget/widget-parts/template',
                array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
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