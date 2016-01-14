<?= $link ?>
<div class="cp-widget-field">
    <label for="<?= $id.'_enablelink'; ?>">
    	<input class="widefat"
	      id="<?= $id.'_enablelink'; ?>"
      name="<?= $name; ?>[enablelink][]"
      type="checkbox"
      value="1" 
      <?php checked( '1', $enable_link ); ?>/>&nbsp;
    	<?php _e('Enable link', 'cppress')?>
    </label>
</div>
<?php if($item !== ''): ?>
<div class="cp-widget-field-repeater-item-change cp-widget-field">
	<?= $item; ?>
</div>
<?php endif; ?>