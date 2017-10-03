<script type="text/html" id='tmpl-cppress-dialog-add-grid'>
	<div class="cp-row-set-form">
		<strong><? _e('Row layout config', 'cppress')?></strong>:
		<input type="number" min="1" max="12" name="" class="cp-row-field" value="2">
	</div>
	<div class="cp-row-preview row cp-rows">
		<div class="cp-grid col-md-6 cp-row-list" data-weight="6">
			<div class="cp-grid-select">
				<input type="number" min="1" max="12" name="" class="cp-grid-select-field" value="6">
				<label><? _e('Select row dimension', 'cppress'); ?></label>
			</div>
			<div class="cp-row-droppable"></div>
		</div>
		<div class="cp-grid col-md-6 cp-row-list" data-weight="6">
			<div class="cp-grid-select">
				<input type="number" min="1" max="12" name="" class="cp-grid-select-field" value="6">
				<label><? _e('Select row dimension', 'cppress'); ?></label>
			</div>
			<div class="cp-row-droppable"></div>
		</div>
	</div>
</script>
<script type="text/html" id='tmpl-cppress-dialog-edit-grid'>
	<div class="cp-row-set-form">
		<strong><? _e('Row layout config', 'cppress')?></strong>:
		<input type="number" min="1" max="12" name="" class="cp-row-field" value="{{ data.count }}">
	</div>
	<div class="cp-row-preview row cp-rows">
		<# for(var i=0; i<data.count; i++){ #>
		<div class="cp-grid col-md-{{ data.weight[i] }} cp-row-list" data-weight="{{ data.weight[i] }}">
			<div class="cp-grid-select">
				<input type="number" min="1" max="12" name="" class="cp-grid-select-field" value="{{ data.weight[i] }}">
				<label><? _e('Select row dimension', 'cppress'); ?></label>
			</div>
			<div class="cp-row-droppable"></div>
		</div>
		<# } #>
	</div>
</script>
<script type="text/html" id='tmpl-cppress-dialog-edit-widget'>
	<div class="cp-row-preview row cp-rows">
		<div class="cp-grid col-md-12 cp-row-list">
			<div id="widget_form" class="cp-row-widget">{{{data.widgetForm}}}</div>
		</div>
	</div>
</script>
<script type="text/html" id='tmpl-cppress-dialog-cell'>
	<div class="cp-grid col-md-{{ data.weight }} cp-row-list">
		<div class="cp-grid-select">
			<input type="number" min="1" max="12" name="" class="cp-grid-select-field" value="{{ data.weight }}">
			<label><? _e('Select row dimension', 'cppress'); ?></label>
		</div>
		<div class="cp-row-droppable"></div>
	</div>
</script>
<script type="text/html" id="tmpl-cppress-sidebar-cell">
	<div class="sidebar-field-wrapper">
		<label>{{data.cell}}. <? _e('Cell classes', 'cppress'); ?></label>
		<div class="sidebar-field sidebar-field-text">
			<div class="sidebar-input-wrapper">
				<input type="text" name="cell{{data.cell}}[classes]" value="{{data.classes}}" class="widefat">
			</div>
			<p class="sidebar-description"><? _e('Cell css classes.', 'cppress'); ?></p>
		</div>
	</div>
	<div class="sidebar-field-wrapper">
		<label>{{data.cell}}. <? _e('Cell style', 'cppress'); ?></label>
		<div class="sidebar-field sidebar-field-code">
			<div class="sidebar-input-wrapper">
				<textarea type="text" name="cell{{data.cell}}[style]" class="widefat cp-field-code" rows="4">{{data.style}}</textarea>
			</div>
			<p class="sidebar-description"><? _e('Cell style', 'cppress'); ?></p>
		</div>
	</div>
</script>
