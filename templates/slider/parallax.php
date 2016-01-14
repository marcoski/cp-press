<?= $media ?>
<?= $sentences ?>
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Parallax options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $fields['id']; ?>_displaytitle">
		    <input class="widefat"
		      id="<?= $fields['id']; ?>_displaytitle"
		      name="<?= $fields['name']; ?>[displaytitle]"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['displaytitle'] ); ?> />&nbsp;
		      <?php _e('Display title', 'cppress')?>
		    </label>
			<div class="cp-widget-field-description">
				<?php _e('Display title field into slider', 'cppress')?>.
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $fields['id']; ?>_displayoverlay">
		    <input class="widefat"
		      id="<?= $fields['id']; ?>_displayoverlay"
		      name="<?= $fields['name']; ?>[displayoverlay]"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['displayoverlay'] ); ?> />&nbsp;
		      <?php _e('Display overlay', 'cppress')?>
		    </label>
			<div class="cp-widget-field-description">
				<?php _e('Display an overlay for the background image', 'cppress')?>.
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label><?php _e('Subtitle', 'cppress'); ?></label>
			<input type="text" class="widefat"
				id="<?= $fields['id']; ?>_subtitle"
				name="<?= $fields['name']; ?>[subtitle]" 
				value="<? isset($values['subtitle']) ? e($values['subtitle']) : e('') ?>"/>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label><?php _e('Next link', 'cppress'); ?></label>
			<input type="text" class="widefat"
				placeholder="External URL or Placeholder name"
				id="<?= $fields['id']; ?>_nextlink"
				name="<?= $fields['name']; ?>[nextlink]" 
				value="<? isset($values['nextlink']) ? e($values['nextlink']) : e('') ?>"/>
			<div class="cp-widget-field-description">
				<?php _e('Link or placeholder for the "Next" slider button', 'cppress')?>.
			</div>
		</div>
	</div>
</div>