<div class="wrap">
	<h2>Commonhelp Press</h2>
	<h2 class="nav-tab-wrapper">
		<a href="?page=cppress-settings&tab=cppress-options-general" class="nav-tab <?php echo $tab === 'cppress-options-general' ? 'nav-tab-active' : ''?>">General</a>
		<a href="?page=cppress-settings&tab=cppress-options-attachment" class="nav-tab <?php echo $tab === 'cppress-options-attachment' ? 'nav-tab-active' : ''?>">Attachment</a>
		<a href="?page=cppress-settings&tab=cppress-options-ldap" class="nav-tab <?php echo $tab === 'cppress-options-ldap' ? 'nav-tab-active' : ''?>">LDAP Settings</a>
        <a href="?page=cppress-settings&tab=cppress-options-apikey" class="nav-tab <?php echo $tab === 'cppress-options-apikey' ? 'nav-tab-active' : ''?>">API Keys</a>
	</h2>
	<form action="options.php" method="post">
	<?php 
		$_settings->fields($tab);
		$_sectionSettingsFactory->render($tab);
	?>
	
	<?php submit_button(); ?>
	</form>
</div>