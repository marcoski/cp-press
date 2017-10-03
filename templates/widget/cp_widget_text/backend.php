<div class="cp-widget-field cp-widget-input">
	<label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e( 'Title', 'cppress' ) ?>:</label>
	<?php echo $template->inc( '/templates/widget/widget-parts/wtitle',
		array( 'widget' => $widget, 'instance' => $instance, 'filter' => $filter ) ); ?>
</div>
<?php echo $editor; ?>
<?php echo $link; ?>
<?php echo $taxonomy; ?>
<div class="cp-widget-field">
	<label for="<?= $widget->get_field_id( 'removep' ); ?>">
		<input class="widefat"
		       id="<?= $widget->get_field_id( 'removep' ); ?>"
		       name="<?= $widget->get_field_name( 'removep' ); ?>"
		       type="checkbox"
		       value="1" <?php checked( '1', $instance['removep'] ); ?> />&nbsp;
		<?php _e( 'Remove p TAGS', 'cppress' ) ?>
	</label>
</div>
<?= $icon; ?>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e( 'Options', 'cppress' ) ?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field">
			<label
				for="<?= $widget->get_field_id( 'maincontainerclass' ); ?>"><?php _e( 'Container class', 'cppress' ) ?>
				:</label>
			<input class="widefat"
			       id="<?= $widget->get_field_id( 'maincontainerclass' ); ?>"
			       name="<?= $widget->get_field_name( 'maincontainerclass' ); ?>"
			       value="<? isset( $instance['maincontainerclass'] ) ? e( $instance['maincontainerclass'] ) : e( '' ); ?>"/>
		</div>
		<div class="cp-widget-field">
			<label
				for="<?= $widget->get_field_id( 'containerclass' ); ?>"><?php _e( 'Text container class', 'cppress' ) ?>
				:</label>
			<input class="widefat"
			       id="<?= $widget->get_field_id( 'containerclass' ); ?>"
			       name="<?= $widget->get_field_name( 'containerclass' ); ?>"
			       value="<? isset( $instance['containerclass'] ) ? e( $instance['containerclass'] ) : e( '' ); ?>"/>
		</div>
		<div class="cp-widget-field">
			<label
				for="<?= $widget->get_field_id( 'linkbutton' ); ?>"><?php _e( 'Make link button', 'cppress' ) ?></label>
			<input class="widefat"
			       id="<?= $widget->get_field_id( 'linkbutton' ); ?>"
			       name="<?= $widget->get_field_name( 'linkbutton' ); ?>"
			       type="checkbox"
			       value="1" <?php checked( '1', $instance['linkbutton'] ); ?> />&nbsp;
			<?php echo $template->inc( '/templates/widget/widget-parts/text/linkbuttontext',
				array( 'widget' => $widget, 'instance' => $instance, 'filter' => $filter ) ); ?>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $widget->get_field_id( 'linktitle' ); ?>">
				<input class="widefat"
				       id="<?= $widget->get_field_id( 'linktitle' ); ?>"
				       name="<?= $widget->get_field_name( 'linktitle' ); ?>"
				       type="checkbox"
				       value="1" <?php checked( '1', $instance['linktitle'] ); ?> />&nbsp;
				<?php _e( 'Link title', 'cppress' ) ?>
			</label>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label
				for="<?= $widget->get_field_id( 'templatename' ); ?>"><?php _e( 'Custom template name', 'cppress' ) ?>
				:</label>
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
				<?php _e( 'Show title', 'cppress' ) ?>
			</label>
		</div>
	</div>
</div>