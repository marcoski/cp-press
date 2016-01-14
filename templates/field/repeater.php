<div class="cp-widget-field cp-widget-field-repeater" 
	data-element-name="<?= $name ?>" 
	data-item-id="<?= $id ?>"
	data-item-title="<?= $item_title ?>"
	data-values="<?= $values ?>"
	data-action="<?= $actions ?>">
	<div class="cp-widget-field-repeater-top">
		<div class="cp-widget-field-repeater-expand"></div>
		<h3><?= $title ?></h3>
	</div>
	<div class="cp-widget-field-repeater-items"></div>
	<div class="cp-widget-field-repeater-add"><?php _e('Add', 'cppress') ?></div>	
	<input type="hidden" name="<?= $name ?>[countitem]" value="" class="cp-widget-field-repeater-counter"/>					
</div>