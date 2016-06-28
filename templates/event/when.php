<div class="cpevent-form-when" id="cpevent-form-when">
	<div class="cpevent-date-range">
		<p><?php _e('From', 'cppress'); ?> <input class="cpevent-date-input cpevent-date-start" type="text" name="cp-press-event[when][event_start_date]" value="<? !is_null($when) ? e(esc_attr($when['event_start_date'])) : e('') ?>" /></p>
		<p><?php _e('To', 'cppress'); ?> <input class="cpevent-date-input cpevent-date-end" type="text" name="cp-press-event[when][event_end_date]" value="<? !is_null($when) ? e(esc_attr($when['event_end_date'])) : e(''); ?>" /></p>
	</div>
	<div class="cpevent-time-range" style="display: none">
		<p>
			<span class="cpevent-event-text"><?php _e('Event starts at', 'cppress'); ?></span>
			<input id="start-time" class="cpevent-time-input cpevent-time-start" type="text" size="8" maxlength="8" name="cp-press-event[when][event_start_time]" value="<? !is_null($when) ? e(esc_attr($when['event_start_time'])) : e(''); ?>" />
		</p>
		<p>
			<?php _e('To', 'cppress'); ?>
			<input id="end-time" class="cpevent-time-input cpevent-time-end" type="text" size="8" maxlength="8" name="cp-press-event[when][event_end_time]" value="<? !is_null($when) ? e(esc_attr($when['event_end_time'])) : e(''); ?>" />
		</p>
		<p><?php _e('All day', 'cppress'); ?> <input type="checkbox" class="cpevent-time-all-day" name="cp-press-event[when][event_all_day]" id="cpevent-time-all-day" value="1"  <?php checked( '1', $when['event_all_day'] ); ?>/></p>
	</div>
</div> 