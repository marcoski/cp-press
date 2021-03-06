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
					<h4 class="modal-title"><?php echo $gallery_title; ?></h4>
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
							<?php
								$lightboxNavLeftClasses = $filter->apply('cppress-lightbox-navleft-classes', [
									'glyphicon', 'glyphicon-chevron-left', 'lightbox-nav-left'
								], $item);
								$lightboxNavRightClasses = $filter->apply('cppress-lightbox-navright-classes', [
									'glyphicon', 'glyphicon-chevron-right', 'lightbox-nav-right'
								], $item);
							?>
							<a href="#" class="<?php echo implode(' ', $lightboxNavLeftClasses); ?>"></a>
							<a href="#" class="<?php echo implode(' ', $lightboxNavRightClasses); ?>"></a>
						</div>
					</div>
				</div>
                <?php if($item['caption'] != ''): ?>
				<div class="modal-footer">
					<?php echo $item['caption'] ?>
				</div>
                <?php endif; ?>
			</div>
		</div>
	</div>
</div>