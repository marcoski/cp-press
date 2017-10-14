<div class="cp-widget-field cp-widget-type-section cp-widget-type-icon-section">	
	<label for="<?= $id['icon'] ?>" class="section <? $visible ? e('cp-widget-type-section-visible') : e(''); ?>"><?php _e('Icon', 'cppress')?></label>
	<div class="cp-widget-section <? !$visible ? e('cp-widget-section-hide') : e(''); ?>">
		<div class="cp-widget-field">
      <label for="<?= $id['icon'] ?>">Icon:</label>
      <div class="cp-widget-icon-selector-current">
        <div class="cp-widget-icon"><span></span></div>
        <label><?php _e('Choose Icon', 'cppress')?></label>
      </div>
      <div class="cp-widget-icon-selector cp-widget-selector">
				<select class="cp-widget-icon-family" >
					<?php foreach( $icon_families as $family_id => $family_info ) : ?>
						<option value="<?php echo esc_attr( $family_id ) ?>"
							<?php selected( $value_family, $family_id ) ?>><?php echo esc_html( $family_info['name'] ) ?> (<?php echo $family_info['iconscount'] ?>)</option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" id="<?= $id['icon'] ?>" name="<?= $name['icon'] ?>" value="<?= $value ?>" class="cp-widget-input-icon"/>
	
				<div class="cp-widget-icon-icons"></div>
			</div>
    </div>
   	<div class="cp-widget-field cp-widget-field-color">
      <label for="<?= $id['color'] ?>"><?php _e('Color', 'cppress')?>:</label>
      <input class="wp-color-picker" id="<?= $id['color'] ?>" name="<?= $name['color'] ?>" value="<?= $color ?>">
    </div>
    <div class="cp-widget-field">
      <label for="<?= $id['class'] ?>"><?php _e('Icon class', 'cppress')?>:</label>
      <input class="widefat" id="<?= $id['class'] ?>" name="<?= $name['class'] ?>" value="<?= $class ?>">
    </div>
    <div class="cp-widget-field">
        <label for="<?= $id['class'] ?>"><?php _e('Position', 'cppress')?>:</label>
        <select
            id="<?php echo $id['iconposition'] ?>"
            name="<?php echo $name['iconposition'] ?>">
            <option value="top" <?php selected($icon_position, 'top'); ?>><?php _e('Top', 'cppress') ?></option>
            <option value="before-title" <?php selected($icon_position, 'before-title'); ?>><?php _e('Before Title', 'cppress') ?></option>
            <option value="after-title" <?php selected($icon_position, 'after-title'); ?>><?php _e('After Title', 'cppress') ?></option>
            <option value="before-content" <?php selected($icon_position, 'before-content'); ?>><?php _e('Before Content', 'cppress') ?></option>
            <option value="after-content" <?php selected($icon_position, 'after-content'); ?>><?php _e('After Content', 'cppress') ?></option>
            <option value="bottom" <?php selected($icon_position, 'top'); ?>><?php _e('Bottom', 'cppress') ?></option>
        </select>
    </div>
  </div>
</div>
  