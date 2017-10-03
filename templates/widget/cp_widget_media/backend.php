<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Image', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-show">
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
	</div>
</div>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Video', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'video' ); ?>"><?php _e('Video URL', 'cppress')?>:</label>
			<input class="widefat"
			       id="<?= $widget->get_field_id( 'video' ); ?>"
			       name="<?= $widget->get_field_name( 'video' ); ?>"
			       value="<? isset( $instance['video'] ) ? e( $instance['video'] ) : e( '' ); ?>"/>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $widget->get_field_id( 'responsive' ); ?>">
				<input
					id="<?= $widget->get_field_id( 'responsive' ); ?>"
					name="<?= $widget->get_field_name( 'responsive' ); ?>"
					type="checkbox"
					value="1" <?php checked( '1', $instance['responsive'] ); ?> />&nbsp;
				<?php _e('Responsive box', 'cppress'); ?>
			</label>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $widget->get_field_id( 'isbackground' ); ?>">
				<input
					id="<?= $widget->get_field_id( 'isbackground' ); ?>"
					name="<?= $widget->get_field_name( 'isbackground' ); ?>"
					type="checkbox"
					value="1" <?php checked( '1', $instance['isbackground'] ); ?> />&nbsp;
				<?php _e('Background video', 'cppress'); ?>
			</label>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $widget->get_field_id( 'templatename' ); ?>"><?php _e('Custom video template', 'cppress')?>:</label>
			<input class="widefat"
			       id="<?= $widget->get_field_id( 'temlatename' ); ?>"
			       name="<?= $widget->get_field_name( 'templatename' ); ?>"
			       value="<? isset( $instance['templatename'] ) ? e( $instance['templatename'] ) : e( '' ); ?>"/>
		</div>
	</div>
</div>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<?php echo $link; ?>
		<?php echo $taxonomy; ?>
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