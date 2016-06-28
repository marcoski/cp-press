<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<?= $news_list ?>
<?= $icon; ?>
<?= $advanced; ?>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('View options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field">
	    <label for="<?= $widget->get_field_id( 'linktitle' ); ?>">
	    	<input class="widefat"
		      id="<?= $widget->get_field_id( 'linktitle' ); ?>"
		      name="<?= $widget->get_field_name( 'linktitle' );; ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['linktitle'] ); ?> />&nbsp;
	    	<?php _e('Link title', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Apply a link to the post title', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $widget->get_field_id( 'hidepdate' ); ?>">
	    	<input class="widefat"
		      id="<?= $widget->get_field_id( 'hidepdate' ); ?>"
		      name="<?= $widget->get_field_name( 'hidepdate' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['hidepdate'] ); ?> />&nbsp;
	    	<?php _e('Hide news publish date', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Hide post publish date', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $widget->get_field_id( 'showexcerpt' ); ?>">
	    	<input class="widefat"
		      id="<?=  $widget->get_field_id( 'showexcerpt' ); ?>"
		      name="<?=  $widget->get_field_name( 'showexcerpt' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['showexcerpt'] ); ?> />&nbsp;
	    	<?php _e('Show excerpt', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Show the post excerpt instead the post content', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?=  $widget->get_field_id( 'hidecontent' ); ?>">
	    	<input class="widefat"
		      id="<?= $widget->get_field_id( 'hidecontent' ); ?>"
		      name="<?= $widget->get_field_name( 'hidecontent' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['hidecontent'] ); ?> />&nbsp;
	    	<?php _e('Hide content', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Hide post content and post excerpt', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $widget->get_field_id( 'hidethumbnail' ); ?>">
	    	<input class="widefat"
		      id="<?= $widget->get_field_id( 'hidethumbnail' ); ?>"
		      name="<?= $widget->get_field_name( 'hidethumbnail' ); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['hidethumbnail'] ); ?> />&nbsp;
	    	<?php _e('Hide post thumbnail', 'cppress')?>
	    </label>
	  </div>
	</div>
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