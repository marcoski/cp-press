<div class="dropdown">
	<p class="dropdown-title drop-down-title-category">
		<span class="dropdown-title-text"><?php echo $label ?></span>
	</p>
	<ul class="dropdown-list">
		<?php foreach($options as $option): ?>
			<li class="dropdown-option">
				<label class="dropdown-label">
					<input type="checkbox" class="dropdown-input" name="category" value="<?php echo $option['value'] ?>" data-type="<?php echo $type ?>" data-input-query="<?php echo htmlspecialchars(json_encode($query, JSON_HEX_TAG)) ?>" />
					<span><?php echo $option['label'] ?></span>
				</label>
			</li>
		<?php endforeach; ?>
	</ul>
</div>