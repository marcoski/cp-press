<div id="cp-press-attachment-inside" data-valid=<?php echo $validMime; ?>>
	<ul class="cp-press-attachment-files"></ul>
	<p class="hide-if-no-js">
		<a href="#" id="cp-press-set-featured-attachment" 
			data-update="<?php _e('Attach', 'cppress'); ?>"
			data-choose="<?php _e('Upload Attachment', 'cppress'); ?>">
			<?php _e('Set new attachment', 'cppress'); ?>
		</a>
	</p>
	<input type="hidden" name="cp-press-attachments" id="cp-press-attachment-input" value="<?= $json_files ?>" />
	<?php wp_nonce_field('save', '_cppress_attachment_nonce') ?>
</div>
<script type="text/html" id='tmpl-cppress-attachment-file'>
	<li>
		<span class="cp-press-attachment-file">
			<i class="cp-widget-icon-delete dashicons-before {{ data.icon }}"></i>
			<i class="cp-widget-icon-featured dashicons-before dashicons-awards"></i>
			{{ data.info }}
		</span>
	</li>
</script>