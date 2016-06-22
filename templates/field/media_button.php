<div class="cp-widget-field cp-widget-field-type-media cp-widget-field-<?= $library ?>">
	<label for="<?= $id['media'] ?>" class="siteorigin-widget-field-label"><?= $lib_title; ?></label>
	<div class="media-field-wrapper">
		<div class="current">
			<div class="thumbnail-wrapper">
				<?php if($media == ''): ?>
				<img src="" class="thumbnail" style="display: none;">
				<?php else: ?>
				<img src="<?= $image[0] ?>" class="thumbnail">
				<?php endif ?>
			</div>
			<div class="title" style="display: none;">
				<?php if($media != ''): ?>
				<?= $image_title; ?>
				<?php endif ?>
			</div>
		</div>
		<a href="#" class="media-upload-button" 
			data-choose="Choose Media" 
			data-update="Set Media" 
			data-library="<?= $library ?>">
			<?php _e('Choose media', 'cppress')?>			
		</a>
	</div>
	<a href="#" class="media-remove-button <?= $remove ?>"><?php _e('Remove', 'cppress')?></a>
	<input type="hidden" value="<?= $media ?>" class="cp-widget-input-media" name="<?= $name['media'] ?>" id="<?= $id['media'] ?>">
	<?php if($external): ?>
	<input type="text" value="<?= $external ?>" placeholder="External URL" class="media-fallback-external" 
		id="<?= $id['external'] ?>"
		name="<?= $name['external'] ?>">
	<?php endif; ?>
	<?php if(isset($name['description']) && $name['description'] !== ''): ?>
	<div class="cp-widget-field-description">
		<?php echo $name['description'] ?>
	</div>
	<?php  endif; ?>
	<div style="clear: both;"></div>
</div>