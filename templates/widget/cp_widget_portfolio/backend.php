<?= $repeater ?>
<div class="cp-widget-field cp-widget-input">
  <label for="<?= $widget->get_field_id( 'wtitle' ); ?>"><?php _e('Title', 'cppress')?>:</label>
  <input class="widefat"
    id="<?= $widget->get_field_id( 'wtitle' ); ?>"
    name="<?= $widget->get_field_name( 'wtitle' ); ?>"
    value="<?= $instance['wtitle']; ?>"
  />
</div>
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field cp-widget-input">
			<label><?php _e('Item thumb size', 'cppress')?></label>
			<input type="number" min="100" max="2048"
				id="<?= $widget->get_field_id( 'thumb_w' ); ?>"
				name="<?= $widget->get_field_name( 'thumb' ); ?>[w]" 
				value="<? isset($instance['thumb']['w']) ? e($instance['thumb']['w']) : 100 ?>"/>
			&nbsp;x&nbsp;
			<input type="number" min="100" max="2048"
				id="<?= $widget->get_field_id( 'thumb_h' ); ?>"
				name="<?= $widget->get_field_name( 'thumb' ); ?>[h]"  
				value="<? isset($instance['thumb']['h']) ? e($instance['thumb']['h']) : 100; ?>"/>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label><?php _e('Items per row', 'cppress'); ?></label>
			<input type="number" min="1" max="6"
				id="<?= $widget->get_field_id( 'itemperrow' ); ?>"
				name="<?= $widget->get_field_name( 'itemperrow' ); ?>" 
				value="<?= $instance['itemperrow'] ?>"/>
		</div>
	</div>
</div>