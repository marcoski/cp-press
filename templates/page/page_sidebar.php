<div class="cppress_dialog-sidebar">
	<? if($type === 'grid'): ?>
	<h3><? _e('Row Styles', 'cppress'); ?></h3>
	<div class="sidebar-section-wrapper">
		<div class="sidebar-section-head">
			<h4><? _e('Config', 'cppress'); ?></h4>
		</div>
		<div class="sidebar-section-fields" style="display: none">
			<div class="sidebar-field-wrapper">
				<label><? _e('Classes', 'cppress'); ?></label>
				<div class="sidebar-field sidebar-field-text">
					<div class="sidebar-input-wrapper">
						<input type="text" name="grid[classes]" value="<?= implode(" ", $args['classes'])?>" class="widefat">
					</div>
					<p class="sidebar-description"><? _e('Space separated classes.', 'cppress'); ?></p>
				</div>
			</div>
			<div class="sidebar-field-wrapper">
				<label><? _e('Style', 'cppress'); ?></label>
				<div class="sidebar-field sidebar-field-code">
					<div class="sidebar-input-wrapper">
						<?
							$style = '';
							foreach($args['style'] as $expr => $value){
								$style .= sprintf("%s: %s;\n", $expr, $value);
							}
						?>
						<textarea type="text" name="grid[style]" class="widefat cp-field-code" rows="4"><?= $style ?></textarea>
					</div>
					<p class="sidebar-description"><? _e('Row style', 'cppress'); ?></p>
				</div>
			</div>
			<div class="sidebar-cell-container">
				<? for($i=0; $i<$args['cell']; $i++): ?>
				<div class="sidebar-field-wrapper">
					<label><?= $i+1; ?>. <? _e('Cell classes', 'cppress'); ?></label>
					<div class="sidebar-field sidebar-field-text">
						<div class="sidebar-input-wrapper">
							<input type="text" name="cell<?=$i+1?>[classes]" value="<?= implode(" ", $args['cellInfo']['classes'][$i]) ?>" class="widefat">
						</div>
						<p class="sidebar-description"><? _e('Cell css classes.', 'cppress'); ?></p>
					</div>
				</div>
				<div class="sidebar-field-wrapper">
					<label><?= $i+1; ?>. <? _e('Cell style', 'cppress'); ?></label>
					<div class="sidebar-field sidebar-field-code">
						<div class="sidebar-input-wrapper">
							<?
								$style = "";
								foreach($args['cellInfo']['style'][$i] as $expr => $value){
									$style .= sprintf("%s: %s;\n", $expr, $value);
								}
							?>
							<textarea type="text" name="cell<?=$i+1?>[style]" class="widefat cp-field-code" rows="4"><?= $style ?></textarea>
						</div>
						<p class="sidebar-description"><? _e('Cell style', 'cppress'); ?></p>
					</div>
				</div>
				<? endfor ?>
			</div>
		</div>
	</div>
  <? endif; ?>
</div>
