<div id="<?php echo $lightboxId ?>" 
			class="cp-lightbox lightbox hide fade"  
			tabindex="-1" role="dialog" 
			aria-hidden="true" 
			data-gallery-selector="<?php echo '#'.$galleryId ?>">
	<div class="lightbox-modal modal-dialog">
		<div class="cp-modal-dialog lightbox-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="lightbox" aria-hidden="true">×</button>
					<h4 class="modal-title"><?= $title ?></h4>
				</div>
				<div class="modal-body">
					<div class="lightbox-content container-fluid cp-lightbox-container">
						<div class="lightbox-<?php echo $item['isvideo'] ? 'video' : 'image' ?>">
						<?php if(!$item['isvideo']): ?>
							<img class="img-responsive hide" src="<?php echo $item['link']; ?>">
						<?php else: ?>
							<img class="img-responsive hide" src="<?php echo $item['oembed']->thumbnail_url; ?>">
						<?php endif; ?>
						</div>
						<div class="cp-lightbox-nav-overlay" style="display: block;">
							<a href="#" class="glyphicon glyphicon-chevron-left lightbox-nav-left" style="line-height: 612px;"></a>
							<a href="#" class="glyphicon glyphicon-chevron-right lightbox-nav-right" style="line-height: 612px;"></a>
						</div>
					</div>
				</div>
				<div class="modal-footer">
				<?php if($item['caption'] != ''): ?>
					<?php echo $item['caption'] ?>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>