<div class="cp-fake-editor-container">
	<?php echo $fake_editor->editor(); ?>
</div>
<div class="cp_press_select_content_type" id="cp_press_select_content_type" data-posttype="<?php echo $post_type ?>" data-postname="<?php echo $post_name ?>">
	<div id="cp_press_rows_head">
		<div id="cp_add_section" data-row="<?php echo esc_attr(count($rows)); ?>" class='button add-section' title='<?php esc_attr_e('Add Section', 'cppress'); ?>'>
			<span class="dashicons-before dashicons-grid-view"></span>
			<?php echo __('Add Section', 'cppress'); ?>
		</div>
		<div id="cp_export_content" class='button add-section' title='<?php esc_attr_e('Export', 'cppress'); ?>'>
			<span class="dashicons-before dashicons-admin-generic"></span>
			<?= __('Export', 'cppress'); ?>
		</div>
		<div id="cp_import_content" class='button add-section' title='<?php esc_attr_e('Import', 'cppress'); ?>'>
			<span class="dashicons-before dashicons-admin-generic"></span>
			<?php echo __('Import', 'cppress'); ?>
			<input class="cp-importer" type="file" id="cp-importer" name="contentimport" style="display:none;" />
		</div>
	</div>
	<input type="hidden" name="cp-press-page-layout" id="cp-press-layout-input" value="<?php echo $json_layout ?>" />
	<?php wp_nonce_field('save', '_cppress_nonce') ?>
</div>
<?php echo $dialog_tmpl; ?>
<?php echo $page_tmpl; ?>
<?php echo $page_dialog_tmpl; ?>
<?php echo $fields_tmpl; ?>
<?php echo $widgets_tmpl; ?>
