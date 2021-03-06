<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <?php echo $template->inc('/templates/widget/widget-parts/wtitle', 
  		array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
</div>
<?= $post_list ?>
<?= $icon ?>
<?= $advanced; ?>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
            <?php echo $template->inc('/templates/widget/widget-parts/template',
                array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $widget->get_field_id( 'hidethumb' ); ?>">
				<input 
				id="<?= $widget->get_field_id( 'hidethumb' ); ?>"
				name="<?= $widget->get_field_name( 'hidethumb' ); ?>" 
				type="checkbox" 
				value="1" <?php checked( '1', $instance['hidethumb'] ); ?> />&nbsp;
				<?php _e('Hide thumbnail', 'cppress')?>
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