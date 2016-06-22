<div class="cp-widget-field cp-widget-input">
  <label for="<?= $id . '_place_' . $count; ?>"><?php _e('Place', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $id . '_place_' . $count; ?>"
    name="<?= $name . '[place][]'; ?>"
    value="<? $values != '' ? e($values['place']) : e('') ?>"
  />
</div>
<?php echo $editor ?>
<div class="cp-widget-field cp-widget-input">
  <label for="<?= $id . '_infomaxwidth_' . $count; ?>"><?php _e('Info Window max width', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $id . '_infomaxwidth_' . $count; ?>"
    name="<?= $name . '[infomaxwidth][]'; ?>"
    value="<? $values != '' ? e($values['infomaxwidth']) : e('') ?>"
  />
</div>