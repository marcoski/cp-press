<div>
	<div class="cp-col col-md-2">
		<div class='thumb' style="background-image: url(<?= $item_img_thumb[0]; ?>); width: <?= $item_img_thumb[1] ?>px; height: <?= $item_img_thumb[2] ?>px;">
		</div>
	</div>
	<div class="cp-col col-md-9">
		<div class="cp-widget-field cp-widget-input" style="margin-top: 0px;">
			<label for="cp-press-portfolio">Title </label>
			<input type='text' disabled="disabled" class="widefat" value='<?= $item_title ?>'/>
		</div>
		<? if($item_content): ?>
		<div class="cp-widget-field cp-widget-input">
			<label for="cp-press-portfolio">Content </label>
			<textarea disabled="disabled" style="width: 100%; height: 80px;"><?= $item_content ?></textarea>
		</div>
		<? endif; ?>
		<div class="cp-widget-field cp-widget-input">
			<label>Link:</label>
			<input disabled="disable" type="text" value="<?= $item_link ?>"/>
		</div>
	</div>
</div>
<div style="clear: both"></div>