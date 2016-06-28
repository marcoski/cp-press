<div class="wrap">
	<h2>Commonhelp Press</h2>
	<?php settings_errors(); ?>
	<h2 class="nav-tab-wrapper">
		<a href="?page=cppress-settings&tab=cppress-options-general" class="nav-tab <?php echo $tab == 'cppress-options-general' ? 'nav-tab-active' : ''?>">General</a>
		<a href="?page=cppress-settings&tab=cppress-options-widget" class="nav-tab <?php echo $tab == 'cppress-options-widget' ? 'nav-tab-active' : ''?>">Widget</a>
		<a href="?page=cppress-settings&tab=cppress-options-attachment" class="nav-tab <?php echo $tab == 'cppress-options-attachment' ? 'nav-tab-active' : ''?>">Attachment</a>
		<a href="?page=cppress-settings&tab=cppress-options-event" class="nav-tab <?php echo $tab == 'cppress-options-event' ? 'nav-tab-active' : ''?>">Event</a>
	</h2>
	<form action="options.php" method="post">
	<?php 
		$_settings->fields($tab);
		$_settings->doSections($tab);
	?>
	
	<?php submit_button(); ?>
	</form>
</div>