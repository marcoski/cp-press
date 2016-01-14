<div class="cp-widget-field cp-widget-accordion">
	<div class="cp-widget-accordion-top">
		<h3><?= $title ?></h3>
	</div>
	<div class="cp-widget-accordions">
		<? 
		for($i=0; $i<count($accordions); $i++){
			if(isset($field['value'][$data[$i]])){
				$style = 'style="display: block"';
				$active = 1;
			}else{
				$style = '';
				$active = 0;
			}
		?>
		<div class="cp-widget-type-accordion">
			<div class="cp-widget-accordion-section-top" data-active="<?= $active; ?>" data-value="<?= $data[$i]; ?>">
				<label class="section"><?= $accordions[$i]; ?></label>
				<div class="cp-widget-accordion-select">
					<input type="checkbox"
						name="<?= $field['name'] ?>[<?= $data[$i] ?>]" 
						value="1" <?php checked( '1', $field['value'][$data[$i]] ); ?>/>
				</div>
			</div>
			<div class="cp-widget-accordion-section" <?= $style ?>>
			<? 
			if(isset($bodies[$i])){
				echo $bodies[$i];
			} 
			?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>