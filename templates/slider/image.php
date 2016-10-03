<?= $images ?>
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Images options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
			<label for="<?= $fields['id']; ?>_displaytitle">
		    <input class="widefat"
		      id="<?= $fields['id']; ?>_displaytitle"
		      name="<?= $fields['name']; ?>[displaytitle]"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['slides']['displaytitle'] ); ?> />&nbsp;
		      <?php _e('Display title', 'cppress')?>
		    </label>
			<div class="cp-widget-field-description">
				<?php _e('Display title field for every slide', 'cppress')?>.
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $fields['id']; ?>_displaycontent">
		    <input class="widefat"
		      id="<?php echo $fields['id']; ?>_displaycontent"
		      name="<?php echo $fields['name']; ?>[displaycontent]"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['slides']['displaycontent'] ); ?> />&nbsp;
		      <?php _e('Display content', 'cppress')?>
		    </label>
			<div class="cp-widget-field-description">
				<?php _e('Display content filed for every slide', 'cppress')?>.
			</div>
		</div>
	</div>
</div>