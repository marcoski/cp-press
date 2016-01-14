<div class="wrap">
	<h2>Commonhelp Press</h2>
	<form action="options.php" method="post">
	<?php 
		$_settings->fields();
		$_settings->doSections();
	?>
	
	<?php submit_button(); ?>
	</form>
</div>