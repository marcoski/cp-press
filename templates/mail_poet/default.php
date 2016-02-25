<form class="subscribe-form" method="post">
	<?php foreach($hiddenFields as $name => $value):?>
		<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
	<?php endforeach; ?>
	<div class="form-group">
		<input type="email" name="cppress-mailpoet-email" class="form-control" placeholder="<?php _e('Enter your email', 'cppress'); ?>">
	</div>
	<input class="btn btn-theme btn-subscribe" type="submit" value="<?php _e('Subscribe', 'cppress'); ?>">
</form>
<?php 
	if($formResult !== null){
		if($formResult['valid']){
			echo '<p>' . $mailpoetConfig->defaults['subscribed_subtitle'] . '</p>';
		}else{
			echo '<p>' . $formResult['message'] . '</p>';
		}
	}