<script type="text/html" id="tmpl-cppress-page">
	<div id="cp_press_rows_container"></div>
</script>
<script type="text/html" id='tmpl-cppress-page-section'>
	<section class="cp-postbox">
		<div class="cp-section-input">
			<input type="text" placeholder="<? esc_attr_e("Title", "cppress") ?>" class="cp-press-section-titleslug" data-model="title" value="">
			<input type="text" placeholder="<? esc_attr_e("Slug", "cppress") ?>" class="cp-press-section-titleslug" data-model="slug" value="">
		</div>
		<div class="cp-toolbar">
			<div class="cp-row-icons cp-section-move" title="<? esc_attr_e('Sort Section', 'cppress'); ?>"></br></div>
			<div class="cp-row-icons cp-row-settings cp-dropdown" title="<? esc_attr_e('Delete Section', 'cppress'); ?>">
				<div class="cp-dropdown-links">
					<ul>
						<li><a href="#" class="cp-section-dropdown" data-action="add"><?= __('Add Row', 'cppress'); ?></a></li>
						<li><a href="#" class="cp-section-dropdown" data-action="duplicate"><?= __('Duplicate Section', 'cppress'); ?></a></li>
						<li><a href="#" class="cp-section-dropdown cp-dropdown-delete" data-action="delete"><?= __('Delete Section', 'cppress'); ?></a></li>
						<div class="cp-dropdown-pointer"></div>
					</ul>
				</div>
			</div>
		</div>
		<div class="cp-grids-container">
		</div>
	</section>
</script>
<script type="text/html" id='tmpl-cppress-page-grid'>
	<div class="cp-grids">
		<div class="cp-toolbar cp-row-toolbar">
			<div class="cp-row-icons cp-row-move" title="<? esc_attr_e('Sort Row', 'cppress'); ?>"></br></div>
            <div class="cp-row-icons cp-row-settings cp-dropdown" title="<? esc_attr_e('Delete Section', 'cppress'); ?>">
                <div class="cp-dropdown-links">
                    <ul>
                        <li><a href="#" class="cp-row-dropdown" data-action="edit"><?= __('Edit Row', 'cppress'); ?></a></li>
                        <li><a href="#" class="cp-row-dropdown" data-action="duplicate"><?= __('Duplicate Row', 'cppress'); ?></a></li>
                        <li><a href="#" class="cp-row-dropdown cp-dropdown-delete" data-action="delete"><?= __('Delete Row', 'cppress'); ?></a></li>
                        <div class="cp-dropdown-pointer"></div>
                    </ul>
                </div>
            </div>
            <div class="cp-row-icons cp-row-plus" title="<? esc_attr_e('Widgets Handling', 'cppress'); ?>"></br></div>
        </div>
			<div class="row cp-rows">
			</div>
		</div>
	</div>
</script>
<script type="text/html" id='tmpl-cppress-page-cell'>
	<div class="cp-grid col-md-{{ data.weight }} cp-row-list">
		<div class="cp-row-droppable cp-widgets-container"></div>
	</div>
</script>
<script type="text/html" id="tmpl-cppress-page-widget">
	<div class="cp-widget">
		<div class="cp-widget-wrapper">
			<div class="title">
				<h4>{{ data.title }}</h4>
                <# if(data.showWidgetOp){ #>
                <span class="actions">
                    <a href="#" class="widget-edit"><?php _e('Edit', 'cppress') ?></a>
                    <a href="#" class="widget-duplicate"><?php _e('Duplicate', 'cppress') ?></a>
                    <a href="#" class="widget-delete"><?php _e('Delete', 'cppress') ?></a>
                </span>
                <# } #>
			</div>
			<small class="description">{{ data.description }}</small>
		</div>
	</div>
</script>
