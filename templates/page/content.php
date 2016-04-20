<div class="cp-fake-editor-container">
	<?= $fake_editor->editor(); ?>
</div>
<div class="cp_press_select_content_type" id="cp_press_select_content_type" data-post="<?= esc_attr($post_id); ?>" data-postname="<?= esc_attr($post_name); ?>">
	<div id="cp_press_rows_head">
		<div id="cp_add_section" data-row="<?= esc_attr(count($rows)); ?>" data-post="<?= esc_attr($post_id); ?>" class='button add-section' title='<? esc_attr_e('Add Section', 'cppress'); ?>'>
			<span class="dashicons-before dashicons-grid-view"></span>
			<?= __('Add Section', 'cppress'); ?>
		</div>
		<div id="cp_export_content" data-post="<?= esc_attr($post_id); ?>" class='button add-section' title='<? esc_attr_e('Export', 'cppress'); ?>'>
			<span class="dashicons-before dashicons-admin-generic"></span>
			<?= __('Export', 'cppress'); ?>
		</div>
		<div id="cp_import_content" data-post="<?= esc_attr($post_id); ?>" class='button add-section' title='<? esc_attr_e('Import', 'cppress'); ?>'>
			<span class="dashicons-before dashicons-admin-generic"></span>
			<?= __('Import', 'cppress'); ?>
			<input class="cp-importer" type="file" id="cp-importer" name="contentimport" style="display:none;" />
		</div>
	</div>
	<input type="hidden" name="cp-press-page-layout" id="cp-press-layout-input" value="<?= $json_layout ?>" />
	<?php wp_nonce_field('save', '_cppress_nonce') ?>
</div>
<?= $dialog_tmpl; ?>
<?= $page_tmpl; ?>
<?= $page_dialog_tmpl; ?>
<?= $fields_tmpl; ?>
