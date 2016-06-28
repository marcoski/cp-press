<div class="row">
	<div class="col-md-6">
		<div class="cp-widget-field">
			<label for="required">
		    <input id="required" name="required" type="checkbox" value="1" />&nbsp;
				<?php _e('Required field', 'cppress')?>
		  </label>
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="name"><?php _e('Name', 'cppress')?>:</label>
		  <input id="name" name="name" value="<?= $name; ?>" type="text" />
		</div>
		<?php if($args['hclass'] != 'select' &&
				$args['hclass'] != 'checkbox' && 
				$args['hclass'] != 'radio' &&
				$args['hclass'] != 'acceptance' &&
				$args['hclass'] != 'file'): ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="default"><?php _e('Default value', 'cppress')?>:</label>
		  <input id="default" name="values" value="" type="text" />
		</div>
		<?php endif; ?>
		<?php if($args['hclass'] == 'select' ||
				$args['hclass'] == 'checkbox' ||
				$args['hclass'] == 'radio'): ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="options"><?php _e('Options', 'cppress')?>:</label>
		  <input id="options" name="values" value="" type="text" />
		  <div class="cp-widget-field-description">
				<?php _e('Insert multiple options separated by space', 'cppress'); ?>
			</div>
		</div>		
		<?php endif; ?>
		<?php if($args['hclass'] == 'file'): ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="filesize"><?php _e('File size limit', 'cppress')?>:</label>
		  <input id="filesize" class="option" name="filesize" value="" type="text" />
		  <div class="cp-widget-field-description">
				<?php _e('Insert file size limit in bytes', 'cppress'); ?>
			</div>
		</div>		
		<?php endif; ?>
	</div>
	<div class="col-md-5">
		<?php if($args['hclass'] == 'select'): ?>
		<div class="cp-widget-field">
			<label for="blankfirst">
		    <input id="blankfirst" class="option" name="blankfirst:on" type="checkbox" value="1" />&nbsp;
				<?php _e('Insert blank item as first element', 'cppress')?>
		  </label>
		</div>
		<div class="cp-widget-field">
			<label for="multiple">
		    <input id="multiple" class="option" name="multiple:on" type="checkbox" value="1" />&nbsp;
				<?php _e('Allow multiple selection', 'cppress')?>
		  </label>
		</div>
		<?php endif; ?>
		<?php if($args['hclass'] == 'acceptance'): ?>
		<div class="cp-widget-field">
			<label for="default">
		    <input id="default" class="option" name="default:on" type="checkbox" value="1" />&nbsp;
				<?php _e('Make this checkbox checked by default', 'cppress')?>
		  </label>
		</div>
		<?php endif; ?>
		<?php if($args['hclass'] == 'checkbox'): ?>
		<div class="cp-widget-field">
			<label for="exclusive">
		    <input id="exclusive" class="option" name="exclusive:on" type="checkbox" value="1" />&nbsp;
				<?php _e('Make checkbox exclusive', 'cppress')?>
		  </label>
		</div>
		<?php endif; ?>
		<?php if($args['hclass'] != 'select' && 
				$args['hclass'] != 'checkbox' && 
				$args['hclass'] != 'radio' &&
				$args['hclass'] != 'acceptance' &&
				$args['hclass'] != 'file'): ?>
		<div class="cp-widget-field">
			<label for="placeholder">
		    <input class="option" id="placeholder" name="placeholder" type="checkbox" value="1" />&nbsp;
				<?php _e('Use default value as placeholder', 'cppress')?>
		  </label>
		</div>
		<?php endif; ?>
		<?php if($args['hclass'] === 'number'): ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="range"><?php _e('Range', 'cppress')?>:</label>
		  <span>Min </span>
		  <input id="number-min" class="numeric option" name="number-min" value="" type="number" /> - <span>Max </span>
		  <input id="number-max" class="numeric option" name="number-max" value="" type="number" />
		</div>
		<?php endif; ?>
		<?php if($args['hclass'] === 'date'): ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="range"><?php _e('Range', 'cppress')?>:</label>
		  <span>Min </span>
		  <input id="date-min" class="date option" name="date-min" value="" type="date" /> - <span>Max </span>
		  <input id="date-max" class="date option" name="date-max" value="" type="date" />
		</div>
		<?php endif; ?>
		<?php if($args['hclass'] == 'file'): ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="filetype"><?php _e('Allowed file', 'cppress')?>:</label>
		  <input id="filetype" class="option" name="filetype" value="" type="text" />
		  <div class="cp-widget-field-description">
				<?php _e('Insert allowed mime types', 'cppress'); ?>
			</div>
		</div>		
		<?php endif; ?>
		<div class="cp-widget-field cp-widget-input">
		  <label for="idattr"><?php _e('ID attribute', 'cppress')?>:</label>
		  <input id="idattr" class="option" name="id" value="" type="text" />
		</div>
		<div class="cp-widget-field cp-widget-input">
		  <label for="classattr"><?php _e('Class attribute', 'cppress')?>:</label>
		  <input id="classattr" class="option" name="classattr" value="" type="text" />
		</div>
	</div>
</div>
<div class="cp-widget-field cp-widget-type-section">
	<label class="section"><?php _e('Shortcode', 'cppress')?></label>
	<div class="cp-widget-section">
		<div class="cp-widget-field cp-widget-input">
		  <input class="widefat cp-widget-title-noedit" id="cp-shortcode-tag-input" value="<?php echo $shortcode; ?>"
		    readonly="readonly"
		  />
		  <input id="cp-tag-type" type="hidden" value="<?php echo $args['hclass']; ?>" />
		</div>
	</div>
</div>