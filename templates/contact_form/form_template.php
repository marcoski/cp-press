<div <?php echo $divAtts?>>
	<form <?php echo $formAtts ?>>
		<?php foreach($hiddenFields as $name => $value):?>
		<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
		<?php endforeach; ?>
		<?php echo $content; ?>
		<?php 
			if($instance['submit'] != ''){
				$submit = $instance['submit'];
			}else{
				$submit = __('Submit', 'cppress');
			}
			$submitClasses = array('btn', 'btn-default');
			echo $filter->apply('cppress-cf-submit', 
				'<button type="submit" value="' . $submit . '" class="' . implode(' ', $submitClasses) . '">' . $submit . '</button>',
				$submitClasses,
				$title	
			); 
		?>
	</form>
</div>