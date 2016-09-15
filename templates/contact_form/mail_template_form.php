<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Mail', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget['to']['id']; ?>"><?php _e('To email address', 'cppress')?>:</label>
		  <input class="widefat" type="text"
		    id="<?= $widget['to']['id']; ?>"
		    name="<?= $widget['to']['name']; ?>"
		    value="<?= $instance['to']; ?>"
		  />
		  <div class="cp-widget-field-description">
				<?php _e('Where contact emails will be delivered to.', 'cppress')?>
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget['from']['id']; ?>"><?php _e('From email address', 'cppress')?>:</label>
		  <input class="widefat" type="text"
		    id="<?= $widget['from']['id']; ?>"
		    name="<?= $widget['from']['name']; ?>"
		    value="<?= $instance['from']; ?>"
		  />
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget['subject']['id']; ?>"><?php _e('Default subject', 'cppress')?>:</label>
		  <?php echo $template->inc('/templates/widget/widget-parts/contact_form/subject', 
  				array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
		  <div class="cp-widget-field-description">
				<?php _e('Subject to use when there isn\'t one available.', 'cppress'); ?>
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget['additionalheaders']['id']; ?>"><?php _e('Additional Headers', 'cppress')?>:</label>
		  <textarea class="large-text code" cols="100" rows="4"
		    id="<?= $widget['additionalheaders']['id']; ?>"
		    name="<?= $widget['additionalheaders']['name']; ?>">
		  	<?= $instance['additionalheaders']; ?>
		  </textarea>
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget['body']['id']; ?>"><?php _e('Message Body', 'cppress')?>:</label>
		  <?php echo $template->inc('/templates/widget/widget-parts/contact_form/body', 
  				array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
		</div>
		<div class="cp-widget-field">
	    <label for="<?= $widget['excludeblank']['id']; ?>">
	    	<input class="widefat"
		      id="<?= $widget['excludeblank']['id']; ?>"
		      name="<?= $widget['excludeblank']['name']; ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['excludeblank'] ); ?> />&nbsp;
	    	<?php _e('Exclude lines with blank mail-tags from output', 'cppress')?>
	    </label>
	  </div>
	  <div class="cp-widget-field">
	    <label for="<?= $widget['usehtml']['id']; ?>">
	    	<input class="widefat"
		      id="<?= $widget['usehtml']['id']; ?>"
		      name="<?= $widget['usehtml']['name']; ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['usehtml'] ); ?> />&nbsp;
	    	<?php _e('Use html mail content', 'cppress')?>
	    </label>
	  </div>
		<?php echo $link; ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget['submit']['id']; ?>"><?php _e('Submit button text', 'cppress')?>:</label>
		  <?php echo $template->inc('/templates/widget/widget-parts/contact_form/submit', 
  				array('widget' => $widget, 'instance' => $instance, 'filter' => $filter)); ?>
		</div>
	</div>
</div>