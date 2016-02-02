<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Advanced options', 'cppress')?></label>
	<div class="cp-widget-section">
		<div class="cp-widget-field">
			<label for="<?= $values['id']['enableadvanced']; ?>">
				<input 
					id="<?= $values['id']['enableadvanced']; ?>"
					name="<?= $values['name']['enableadvanced']; ?>" 
					type="checkbox" 
					value="1" <?php checked( '1', $values['value']['enableadvanced'] ); ?> />
				<?php _e('Enable advanced options', 'cppress')?>
			</label>
		</div>
		<? if(!$single): ?>
		<div class="cp-widget-field">
			<label for="<?= $values['id']['limit'] ?>"><?php _e('Limit', 'cppress')?>:</label>
			<input type="number" min="1" id="<?= $values['id']['limit'] ?>"
				name="<?= $values['name']['limit']; ?>"  
				value="<? $values['value']['limit'] != '' ? e($values['value']['limit']) : e('1') ?>"/>
		</div>
		<? endif; ?>
		<div class="cp-widget-field">
			<label <?= $values['id']['offset'] ?>"><?php _e('Offset', 'cppress')?>:</label>
			<input type="number" min="0" id="<?= $values['id']['offset'] ?>"
				name="<?= $values['name']['offset']; ?>"  
				value="<? $values['value']['offset'] != '' ? e($values['value']['offset']) : e('0') ?>"/>
				<div class="cp-widget-field-description">
					<?php _e('The number of post to skip', 'cppress')?>
				</div>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Order', 'cppress')?>:</label>
			<select style="width:100%;" id="<?= $values['id']['order'] ?>"
				name="<?= $values['name']['order']; ?>" >
				<option value="DESC" <?php selected( $values['value']['order'], 'DESC' ); ?>><?php _e('Descending', 'cppress'); ?></option>
				<option value="ASC" <?php selected( $values['value']['order'], 'ASC' ); ?>><?php _e('Ascending', 'cppress'); ?></option>
			</select>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Orderby', 'cppress')?>:</label>
			<select class="widefat" style="width:100%;" id="<?= $values['id']['orderby'] ?>"
				name="<?= $values['name']['orderby']; ?>" >
				<option value="ID" <?php selected( $values['value']['orderby'], 'ID' ); ?>><?php _e('ID', 'cppress'); ?></option>
				<option value="author" <?php selected( $values['value']['orderby'], 'author' ); ?>><?php _e('Author', 'cppress'); ?></option>
				<option value="title" <?php selected( $values['value']['orderby'], 'title' ); ?>><?php _e('Title', 'cppress'); ?></option>
				<option value="date" <?php selected( $values['value']['orderby'], 'date' ); ?>><?php _e('Date' , 'cppress'); ?></option>
				<option value="modified" <?php selected( $values['value']['orderby'], 'modified' ); ?>><?php _e('Modified', 'cppress'); ?></option>
				<option value="rand" <?php selected( $values['value']['orderby'], 'rand' ); ?>><?php _e('Random', 'cppress'); ?></option>
				<option value="comment_count" <?php selected( $values['value']['orderby'], 'comment_count' ); ?>><?php _e('Comment Count', 'cppress'); ?></option>
				<option value="menu_order" <?php selected($values['value']['orderby'], 'menu_order' ); ?>><?php _e('Menu Order', 'cppress'); ?></option>
			</select>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Filter by Calendar', 'cppress')?>:</label>
			<select multiple="multiple" style="width:100%;" id="<?= $values['id']['calendars'] ?>"
				name="<?= $values['name']['calendars']; ?>[]">
				<optgroup label="<?php _e('Calendars', 'cppress'); ?>">
					<?php $calendars = get_terms('calendar', array('hide_empty' => false)); ?>
					<?php foreach( $calendars as $calendar ) { ?>
						<option value="<?php echo $calendar->term_id; ?>" 
							<?php if (is_array( $values['value']['calendars']) && 
									in_array( $calendar->term_id, $values['value']['calendars'])){
										echo ' selected="selected"';
									} ?>><?php echo $calendar->name; ?></option>
					<?php } ?>
				</optgroup>
			</select>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Filter by Event Tag', 'cppress')?>:</label>
			<select style="width:100%;" multiple="multiple" id="<?= $values['id']['tags'] ?>"
				name="<?= $values['name']['tags']; ?>[]">
				<optgroup label="<?php _e('Event Tags', 'cppress'); ?>">
					<?php $tags = get_terms('event-tags', array('hide_empty' => false)); ?>
					<?php foreach( $tags as $post_tag ) { ?>
						<option value="<?php echo $post_tag->term_id; ?>" 
							<?php if (is_array($values['value']['tags']) && 
									in_array($post_tag->term_id, $values['value']['tags'])){
										echo ' selected="selected"'; 
									} ?>><?php echo $post_tag->name; ?></option>
					<?php } ?>
				</optgroup>
			</select>
		</div>
	</div>
</div>