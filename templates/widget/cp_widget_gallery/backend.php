<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<?= $repeater ?>
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Controls', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
			<label><?php _e('Template', 'cppress'); ?></label>
			<select name="<?= $widget->get_field_name( 'template' ); ?>" >
				<option value="carousel" <? selected( $instance['template'], 'carousel' ); ?>><?php _e('Carousel', 'cppress'); ?></option>
				<option value="list" <? selected( $instance['template'], 'glist' ); ?>><?php _e('List', 'cppress') ?></option>
			</select>
			<div class="cp-widget-field-description">
				<?php _e('Gallery frontend template. Default is bootstrap carousel', 'cppress')?>.
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label>
		    <input class="widefat"
		      name="<?= $widget->get_field_name( 'enablelightbox' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['enablelightbox'] ); ?> />&nbsp;
		      <?php _e('Enable lightbox', 'cppress')?>
		    </label>
			<div class="cp-widget-field-description">
				<?php _e('Enable lightbox slideshow on image click', 'cppress')?>.
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label><?php _e('Thumbs per row', 'cppress'); ?></label>
			<input type="number" min="1" max="12"
				id="<?= $widget->get_field_id( 'tperrow' ); ?>"
				name="<?= $widget->get_field_name( 'tperrow' ); ?>" 
				value="<? $instance['tperrow'] != '' ? $istance['tperrow'] : e('3') ?>"/>
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