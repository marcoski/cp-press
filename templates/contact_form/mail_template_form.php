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
		  <label for="<?= $widget['subject']['id']; ?>"><?php _e('Default subject', 'cppress')?>:</label>
		  <input class="widefat" type="text"
		    id="<?= $widget['subject']['id']; ?>"
		    name="<?= $widget['subject']['name']; ?>"
		    value="<?= $instance['subject']; ?>"
		  />
		  <div class="cp-widget-field-description">
				<?php _e('Subject to use when there isn\'t one available.', 'cppress'); ?>
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget['subjectpre']['id']; ?>"><?php _e('Subject prefix', 'cppress')?>:</label>
		  <input class="widefat" type="text"
		    id="<?= $widget['subjectpre']['id']; ?>"
		    name="<?= $widget['subjectpre']['name']; ?>"
		    value="<?= $instance['subjectpre']; ?>"
		  />
		  <div class="cp-widget-field-description">
				<?php _e('Prefix added to all incoming email subjects.', 'cppress'); ?>
			</div>
		</div>
		<?php echo $link; ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="<?= $widget['submit']['id']; ?>"><?php _e('Submit button text', 'cppress')?>:</label>
		  <input class="widefat" type="text"
		    id="<?= $widget['submit']['id']; ?>"
		    name="<?= $widget['submit']['name']; ?>"
		    value="<? echo $instance['submit'] != '' ? $instance['submit'] : __('Contact Us', 'cppress'); ?>"
		  />
		</div>
	</div>
</div>